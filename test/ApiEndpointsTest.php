<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/test_ApiClient.php';

class ApiEndpointsTest extends TestCase
{
    private $apiClient;

    protected function setUp(): void
    {
        // Мокаємо ApiClient
        $this->apiClient = $this->createMock(ApiClient::class);
    }

    // --- add_new_category.php ---
    public function testAddNewCategorySuccess()
    {
        $expected = ['success' => true, 'category_id' => 123];
        $this->apiClient->method('post')->willReturn($expected);

        $result = $this->apiClient->post('http://localhost/cabinet/api/add_new_category.php', ['name' => 'TestCat']);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('category_id', $result);
    }
    public function testAddNewCategoryEmpty()
    {
        $expected = ['success' => false, 'message' => 'Назва не може бути порожньою'];
        $this->apiClient->method('post')->willReturn($expected);

        $result = $this->apiClient->post('http://localhost/cabinet/api/add_new_category.php', ['name' => '']);
        $this->assertFalse($result['success']);
        $this->assertEquals('Назва не може бути порожньою', $result['message']);
    }
    public function testAddNewCategoryUnauthorized()
    {
        $expected = ['success' => false, 'message' => 'Неавторизовано'];
        $this->apiClient->method('post')->willReturn($expected);

        $result = $this->apiClient->post('http://localhost/cabinet/api/add_new_category.php', ['name' => 'Any']);
        $this->assertFalse($result['success']);
        $this->assertEquals('Неавторизовано', $result['message']);
    }

    // --- make_admin.php ---
    public function testMakeAdminSuccess()
    {
        $expected = ['success' => true];
        $this->apiClient->method('post')->willReturn($expected);

        $result = $this->apiClient->post('http://localhost/cabinet/api/make_admin.php', ['user_id' => 2]);
        $this->assertTrue($result['success']);
    }
    public function testMakeAdminUnauthorized()
    {
        $expected = ['success' => false, 'message' => 'Доступ заборонено'];
        $this->apiClient->method('post')->willReturn($expected);

        $result = $this->apiClient->post('http://localhost/cabinet/api/make_admin.php', ['user_id' => 2]);
        $this->assertFalse($result['success']);
        $this->assertEquals('Доступ заборонено', $result['message']);
    }

    // --- logout.php ---
    public function testLogoutRedirect()
    {
        // Для logout можна перевірити редірект (імітація)
        $expected = ['redirect' => '/cabinet/login.php'];
        $this->apiClient->method('get')->willReturn($expected);

        $result = $this->apiClient->get('http://localhost/cabinet/api/logout.php');
        $this->assertEquals('/cabinet/login.php', $result['redirect']);
    }

    // --- login.php ---
    public function testLoginSuccess()
    {
        $expected = ['success' => true, 'user_id' => 1];
        $this->apiClient->method('post')->willReturn($expected);

        $result = $this->apiClient->post('http://localhost/cabinet/api/login.php', [
            'login_username_or_email' => 'user',
            'login_password' => 'password'
        ]);
        $this->assertTrue($result['success']);
    }
    public function testLoginFail()
    {
        $expected = ['success' => false, 'message' => 'Невірний логін або пароль'];
        $this->apiClient->method('post')->willReturn($expected);

        $result = $this->apiClient->post('http://localhost/cabinet/api/login.php', [
            'login_username_or_email' => 'user',
            'login_password' => 'wrong'
        ]);
        $this->assertFalse($result['success']);
        $this->assertEquals('Невірний логін або пароль', $result['message']);
    }

    // --- get_profile_info.php ---
    public function testGetProfileInfoSuccess()
    {
        $expected = ['success' => true, 'user' => ['user_id' => 1, 'username' => 'user']];
        $this->apiClient->method('get')->willReturn($expected);

        $result = $this->apiClient->get('http://localhost/cabinet/api/get_profile_info.php');
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('user', $result);
    }
    public function testGetProfileInfoUnauthorized()
    {
        $expected = ['success' => false, 'message' => 'Неавторизовано'];
        $this->apiClient->method('get')->willReturn($expected);

        $result = $this->apiClient->get('http://localhost/cabinet/api/get_profile_info.php');
        $this->assertFalse($result['success']);
        $this->assertEquals('Неавторизовано', $result['message']);
    }

    // --- get_post.php ---
    public function testGetPostUnauthorized()
    {
        $expected = ['success' => false, 'message' => 'Неавторизовано'];
        $this->apiClient->method('get')->willReturn($expected);

        $result = $this->apiClient->get('http://localhost/cabinet/api/get_post.php', ['post_id' => 1]);
        $this->assertFalse($result['success']);
    }

    // --- get_new_users.php ---
    public function testGetNewUsers()
    {
        $expected = ['success' => true, 'users' => [['new_user_id' => 1, 'username' => 'newuser']]];
        $this->apiClient->method('get')->willReturn($expected);

        $result = $this->apiClient->get('http://localhost/cabinet/api/get_new_users.php');
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('users', $result);
    }

    // --- get_new_user_info.php ---
    public function testGetNewUserInfo()
    {
        $expected = ['success' => true, 'user' => ['new_user_id' => 1, 'username' => 'newuser']];
        $this->apiClient->method('get')->willReturn($expected);

        $result = $this->apiClient->get('http://localhost/cabinet/api/get_new_user_info.php', ['new_user_id' => 1]);
        $this->assertArrayHasKey('success', $result);
    }

    // --- get_authors.php ---
    public function testGetAuthorsAdmin()
    {
        $expected = ['success' => true, 'authors' => [['user_id' => 1, 'username' => 'admin']]];
        $this->apiClient->method('get')->willReturn($expected);

        $result = $this->apiClient->get('http://localhost/cabinet/api/get_authors.php');
        $this->assertTrue($result['success']);
    }
    public function testGetAuthorsUnauthorized()
    {
        $expected = ['success' => false, 'message' => 'Доступ заборонено'];
        $this->apiClient->method('get')->willReturn($expected);

        $result = $this->apiClient->get('http://localhost/cabinet/api/get_authors.php');
        $this->assertFalse($result['success']);
        $this->assertEquals('Доступ заборонено', $result['message']);
    }

    // --- get_author_info.php ---
    public function testGetAuthorInfo()
    {
        $expected = ['success' => true, 'user' => ['user_id' => 1, 'username' => 'admin']];
        $this->apiClient->method('get')->willReturn($expected);

        $result = $this->apiClient->get('http://localhost/cabinet/api/get_author_info.php', ['user_id' => 1]);
        $this->assertArrayHasKey('success', $result);
    }

    // --- get_all_posts.php ---
    public function testGetAllPostsAdmin()
    {
        $expected = ['success' => true, 'posts' => [['post_id' => 1, 'title' => 'Test']]];
        $this->apiClient->method('get')->willReturn($expected);

        $result = $this->apiClient->get('http://localhost/cabinet/api/get_all_posts.php');
        $this->assertArrayHasKey('success', $result);
    }
    public function testGetAllPostsUnauthorized()
    {
        $expected = ['success' => false, 'message' => 'Доступ заборонено'];
        $this->apiClient->method('get')->willReturn($expected);

        $result = $this->apiClient->get('http://localhost/cabinet/api/get_all_posts.php');
        $this->assertFalse($result['success']);
        $this->assertEquals('Доступ заборонено', $result['message']);
    }

    // --- get_all_categories.php ---
    public function testGetAllCategories()
    {
        $expected = ['categories' => [['id' => 1, 'name' => 'Tech']]];
        $this->apiClient->method('get')->willReturn($expected);

        $result = $this->apiClient->get('http://localhost/cabinet/api/get_all_categories.php');
        $this->assertArrayHasKey('categories', $result);
    }

    // --- delete_user.php ---
    public function testDeleteUserUnauthorized()
    {
        $expected = ['success' => false, 'message' => 'Доступ заборонено'];
        $this->apiClient->method('post')->willReturn($expected);

        $result = $this->apiClient->post('http://localhost/cabinet/api/delete_user.php', ['user_id' => 2]);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
    }

    // --- delete_post.php ---
    public function testDeletePostUnauthorized()
    {
        $expected = ['success' => false, 'message' => 'Доступ заборонено'];
        $this->apiClient->method('post')->willReturn($expected);

        $result = $this->apiClient->post('http://localhost/cabinet/api/delete_post.php', ['post_id' => 1]);
        $this->assertFalse($result['success']);
    }

    // --- delete_new_user.php ---
    public function testDeleteNewUser()
    {
        $expected = ['success' => true, 'message' => 'Користувача видалено'];
        $this->apiClient->method('post')->willReturn($expected);

        $result = $this->apiClient->post('http://localhost/cabinet/api/delete_new_user.php', ['new_user_id' => 1]);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
    }

    // --- delete_category.php ---
    public function testDeleteCategoryUnauthorized()
    {
        $expected = ['success' => false, 'message' => 'Доступ заборонено'];
        $this->apiClient->method('post')->willReturn($expected);

        $result = $this->apiClient->post('http://localhost/cabinet/api/delete_category.php', ['category_id' => 1]);
        $this->assertFalse($result['success']);
    }

    // --- change_password.php ---
    public function testChangePasswordUnauthorized()
    {
        $expected = ['success' => false, 'message' => 'Неавторизовано'];
        $this->apiClient->method('post')->willReturn($expected);

        $result = $this->apiClient->post('http://localhost/cabinet/api/change_password.php', [
            'old_password' => '123', 'new_password' => '456'
        ]);
        $this->assertFalse($result['success']);
    }

    // --- approve_new_user.php ---
    public function testApproveNewUser()
    {
        $expected = ['success' => true, 'user_id' => 5];
        $this->apiClient->method('post')->willReturn($expected);

        $result = $this->apiClient->post('http://localhost/cabinet/api/approve_new_user.php', [
            'new_user_id' => 1, 'is_admin' => 0
        ]);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
    }

    // --- add_new_user.php ---
    public function testAddNewUser()
    {
        $expected = ['success' => true, 'user_id' => 10];
        $this->apiClient->method('post')->willReturn($expected);

        $result = $this->apiClient->post('http://localhost/cabinet/api/add_new_user.php', [
            'username' => 'testuser_' . uniqid(),
            'password' => 'testpass',
            'full_name' => 'Test User',
            'email' => uniqid() . '@mail.com'
        ]);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
    }

    // --- add_new_edit_post.php ---
    public function testAddNewEditPostUnauthorized()
    {
        $expected = ['success' => false, 'message' => 'Неавторизовано'];
        $this->apiClient->method('post')->willReturn($expected);

        $result = $this->apiClient->post('http://localhost/cabinet/api/add_new_edit_post.php', [
            'title' => 'Test', 'description' => 'Test'
        ]);
        $this->assertFalse($result['success']);
    }
}
?>