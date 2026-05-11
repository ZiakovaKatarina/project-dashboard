<?php

namespace App\Controllers;

use App\Configuration;
use App\Models\User;
use Exception;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use Framework\Http\Responses\ViewResponse;

/**
 * Class AuthController
 *
 * This controller handles authentication actions such as login, logout, and redirection to the login page. It manages
 * user sessions and interactions with the authentication system.
 *
 * @package App\Controllers
 */
class AuthController extends BaseController
{
    /**
     * Redirects to the login page.
     *
     * This action serves as the default landing point for the authentication section of the application, directing
     * users to the login URL specified in the configuration.
     *
     * @return Response The response object for the redirection to the login page.
     */
    public function index(Request $request): Response
    {
        return $this->redirect(Configuration::LOGIN_URL);
    }

    /**
     * Authenticates a user and processes the login request.
     *
     * This action handles user login attempts. If the login form is submitted, it attempts to authenticate the user
     * with the provided credentials. Upon successful login, the user is redirected to the admin dashboard.
     * If authentication fails, an error message is displayed on the login page.
     *
     * @return Response The response object which can either redirect on success or render the login view with
     *                  an error message on failure.
     * @throws Exception If the parameter for the URL generator is invalid throws an exception.
     */
    public function login(Request $request): Response
    {
        if (!$request->isPost()) {
            return $this->html();
        }

        $body = $request->json();
        $username = trim($body->username);
        $password = $body->password;
        
        $success = $this->app->getAuthenticator()->login($username, $password);
        if ($success) {
            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false]);
    }

    /**
     * Logs out the current user.
     *
     * This action terminates the user's session and redirects them to a view. It effectively clears any authentication
     * tokens or session data associated with the user.
     *
     * @return ViewResponse The response object that renders the logout view.
     */
    public function logout(Request $request): Response
    {
        $this->app->getAuthenticator()->logout();
        return $this->html();
    }

    public function register(Request $request): Response
    {
        $error = "";

        if ($request->isPost()) {
            $email = trim($request->value('email'));
            $users = User::getAll('`email` = ?', [$email]);
            if ($users) {
                $error = 'Používateľ už s takýmto emailom existuje.';
            } else {
                $first_password = $request->value('password');
                $second_password = $request->value('repeated_password');
                if ($first_password !== $second_password) {
                    $error = 'Heslá sa nezhodujú.';
                } else {
                    $hashed_password = password_hash($first_password, PASSWORD_DEFAULT);

                    $first_name = $request->value('first_name');
                    $last_name = $request->value('last_name');

                    $user = new User();
                    $user->setAdmin(0);
                    $user->setPassword($hashed_password);
                    $user->setFirstName($first_name);
                    $user->setEmail($email);
                    $user->setLastName($last_name);
                    $user->save();
                    
                    return $this->redirect($this->url('auth.login'));
                }   
            }
        }

        return $this->html(['error' => $error]);
    }
}
