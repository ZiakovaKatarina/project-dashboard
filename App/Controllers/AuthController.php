<?php

namespace App\Controllers;

use App\Configuration;
use App\Models\User;
use Exception;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use Framework\Http\Responses\ViewResponse;
use Override;

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
    #[Override]
    public function authorize(Request $request, string $action): bool
    {
        if ($action == 'index' || $action == 'login' || $action == 'register' || $action == 'profile' || $action == 'update') {
            return true;
        }

        if ($this->app->getAppUser()->isLoggedIn() && $action == 'logout') {
            return true;
        }

        return false;
    }
    
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
        $errors = array();

        if ($request->isPost()) {
            $email = trim($request->value('email'));
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email nie je v správnom tvare.';
            }
            if (mb_strlen($email) > 250) {
                $errors[] = 'Email nemôže byť dlhší ako 250 znakov.';
            }

            $users = User::getAll('`email` = ?', [$email]);
            if ($users) {
                $errors[] = 'Používateľ už s takýmto emailom existuje.';
            } 
            
            $first_password = $request->value('password');
            $second_password = $request->value('repeated_password');
            if ($first_password !== $second_password) {
                $errors[] = 'Heslá sa nezhodujú.';
            } elseif (mb_strlen($first_password) < 5 || mb_strlen($first_password) > 250) {
                $errors[] = 'Heslo nemôže byť kratšie ako 5 znakov a dlhšie ako 250 znakov.';
            }
            
            $first_name = trim($request->value('first_name'));
            if (empty($first_name)) {
                $errors[] = 'Meno nemôže byť prázdne.';
            } elseif (mb_strlen($first_name) > 100 || mb_strlen($first_name) < 3) {
                $errors[] = 'Dĺžka mena musí byť v rozmedzí od 3 do 100 znakov.';
            }

            $last_name = trim($request->value('last_name'));
            if (empty($last_name)) {
                $errors[] = 'Priezvisko nemôže byť prázdne.';
            } elseif (mb_strlen($last_name) > 100 || mb_strlen($last_name) < 3) {
                $errors[] = 'Priezvisko musí byť v rozmedzí od 3 do 100 znakov.';
            }

            $user = new User();
            $user->setAdmin(0);
            $user->setFirstName($first_name);
            $user->setLastName($last_name);
            $user->setEmail($email);

            if (count($errors) > 0) {
                return $this->html(['errors' => $errors, 'register_user' => $user], 'register');
            }

            $hashed_password = password_hash($first_password, PASSWORD_DEFAULT);
            $user->setPassword($hashed_password);
            $user->save();
                    
            return $this->redirect($this->url('auth.login'));
        }   

        return $this->html(['errors' => $errors]);
    }

    public function profile(Request $request): Response
    {
        $user = User::getOne($this->user->getId());
        return $this->html(['register_user' => $user], 'profile');
    }

    public function update(Request $request): Response
    {
        $errors = array();

        if ($request->isPost()) {
            $user = User::getOne($this->user->getId());
            
            $first_password = $request->value('password');
            if (!empty($first_password)) {
                $second_password = $request->value('repeated_password');
                if ($first_password !== $second_password) {
                    $errors[] = 'Heslá sa nezhodujú.';
                } elseif (mb_strlen($first_password) < 5 || mb_strlen($first_password) > 250) {
                    $errors[] = 'Heslo nemôže byť kratšie ako 5 znakov a dlhšie ako 250 znakov.';
                }

                $hashed_password = password_hash($first_password, PASSWORD_DEFAULT);
                $user->setPassword($hashed_password);
            }

            $email = $request->value('email');
            if (strcmp($email, $user->getEmail()) != 0) {
                $users = User::getAll('`email` = ?', [$email]);
                if ($users) {
                    $errors[] = 'Používateľ už s takýmto emailom existuje.';
                }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'Email nie je v správnom tvare.';
                }
                if (mb_strlen($email) > 250) {
                    $errors[] = 'Email nemôže byť dlhší ako 250 znakov.';
                }
            }
            
            
            $first_name = trim($request->value('first_name'));
            if (empty($first_name)) {
                $errors[] = 'Meno nemôže byť prázdne.';
            } elseif (mb_strlen($first_name) > 100 || mb_strlen($first_name) < 3) {
                $errors[] = 'Dĺžka mena musí byť v rozmedzí od 3 do 100 znakov.';
            }

            $last_name = trim($request->value('last_name'));
            if (empty($last_name)) {
                $errors[] = 'Priezvisko nemôže byť prázdne.';
            } elseif (mb_strlen($last_name) > 100 || mb_strlen($last_name) < 3) {
                $errors[] = 'Priezvisko musí byť v rozmedzí od 3 do 100 znakov.';
            }

            $user->setFirstName($first_name);
            $user->setLastName($last_name);
            $user->setEmail($email);

            if (count($errors) > 0) {
                return $this->html(['errors' => $errors, 'register_user' => $user], 'profile');
            }

            $user->save();
                    
            return $this->redirect($this->url('auth.profile'));
        }   

        return $this->html(['errors' => $errors]);
    }
}
