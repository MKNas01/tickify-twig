<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();  // For $_SESSION sim

error_log('Current session: ' . print_r($_SESSION, true));

require_once 'vendor/autoload.php';  // Composer autoload

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Load Twig
$loader = new FilesystemLoader();
$loader->addPath(__DIR__ . '/templates');
$loader->addPath(__DIR__ . '/includes');  // Add includes dir

$twig = new Environment($loader, [
    'cache' => false,  // Disable cache for dev
]);

// Simple router (switch path)
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($requestUri, '/');

// Protected check helper
function isAuthenticated() {
    return isset($_SESSION['ticketapp_session']);
}

// Route logic
switch ($path) {
    case '':
    case 'landing':
        echo $twig->render('landing.twig');
        break;
    case 'auth/login':
        $email_error = $password_error = $error = '';
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Server-side validation
            if (empty($email)) {
                $email_error = 'Email is required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email_error = 'Invalid email format';
            }

            if (empty($password)) {
                $password_error = 'Password is required';
            } elseif (strlen($password) < 6) {
                $password_error = 'Password must be at least 6 characters';
            }

            if (empty($email_error) && empty($password_error)) {
                $storedUser = $_SESSION['tickify_user'] ?? null;
                if (!$storedUser || $storedUser['email'] !== $email || $storedUser['password'] !== $password) {
                    $error = 'Invalid credentials. Please try again.';
                } else {
                    $_SESSION['ticketapp_session'] = ['email' => $email];
                    header('Location: /dashboard');
                    exit;
                }
            }
        }

        echo $twig->render('login.twig', [
            'email' => $email,
            'email_error' => $email_error,
            'password_error' => $password_error,
            'error' => $error
        ]);
        break;
    case 'auth/signup':
        $email_error = $password_error = $confirm_error = $error = '';
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm'] ?? '';

            // Server-side validation
            if (empty($email)) {
                $email_error = 'Email is required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email_error = 'Invalid email format';
            }

            if (empty($password)) {
                $password_error = 'Password is required';
            } elseif (strlen($password) < 6) {
                $password_error = 'Password must be at least 6 characters';
            }

            if (empty($confirm)) {
                $confirm_error = 'Please confirm your password';
            } elseif ($password !== $confirm) {
                $confirm_error = 'Passwords do not match';
            }

            if (empty($email_error) && empty($password_error) && empty($confirm_error)) {
                $storedUser = $_SESSION['tickify_user'] ?? null;
                if ($storedUser && $storedUser['email'] === $email) {
                    $error = 'An account with this email already exists.';
                } else {
                    $_SESSION['tickify_user'] = ['email' => $email, 'password' => $password];
                    header('Location: /auth/login');
                    exit;
                }
            }
        }

        echo $twig->render('signup.twig', [
            'email' => $email,
            'email_error' => $email_error,
            'password_error' => $password_error,
            'confirm_error' => $confirm_error,
            'error' => $error
        ]);
        break;
    case 'dashboard':
        if (!isAuthenticated()) {
            header('Location: /auth/login');
            exit;
        }

        // Debug: Log session
        error_log('Session data: ' . print_r($_SESSION, true));

        $user = $_SESSION['tickify_user'] ?? ['email' => 'test@example.com'];  // Fallback
        $tickets = $_SESSION['tickify_tickets'] ?? [];

        $totalTickets = count($tickets);
        $openTickets = count(array_filter($tickets, function($t) { return $t['status'] === 'open'; }));
        $resolvedTickets = count(array_filter($tickets, function($t) { return $t['status'] === 'closed'; }));

        echo $twig->render('dashboard.twig', [
            'user' => $user,
            'totalTickets' => $totalTickets,
            'openTickets' => $openTickets,
            'resolvedTickets' => $resolvedTickets
        ]);
        break;
    case 'tickets':
        if (!isAuthenticated()) {
            header('Location: /auth/login');
            exit;
        }

        $tickets = $_SESSION['tickify_tickets'] ?? [];
        $error = $title_error = $description_error = $status_error = '';
        $form = ['title' => '', 'description' => '', 'status' => 'open'];
        $edit_mode = false;
        $edit_ticket = null;

        // Handle GET for edit
        if (isset($_GET['edit'])) {
            $edit_id = intval($_GET['edit']);
            foreach ($tickets as $ticket) {
                if ($ticket['id'] == $edit_id) {
                    $form = $ticket;
                    $edit_mode = true;
                    $edit_ticket = $ticket;
                    break;
                }
            }
        }

        // Handle POST for CRUD
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $title = trim($_POST['title'] ?? '');
            $description = $_POST['description'] ?? '';
            $status = $_POST['status'] ?? 'open';
            $id = intval($_POST['id'] ?? 0);

            // Validation
            if (empty($title)) {
                $title_error = 'Title is required';
            }
            if (!in_array($status, ['open', 'in_progress', 'closed'])) {
                $status_error = 'Invalid status';
            }
            if (!empty($description) && strlen($description) < 10) {
                $description_error = 'Description must be at least 10 characters';
            }

            if (empty($title_error) && empty($status_error) && empty($description_error)) {
                if ($action === 'create') {
                    $tickets[] = [
                        'id' => time(),
                        'title' => $title,
                        'description' => $description,
                        'status' => $status,
                        'createdAt' => date('Y-m-d H:i:s')
                    ];
                } elseif ($action === 'update' && $id > 0) {
                    foreach ($tickets as &$ticket) {
                        if ($ticket['id'] == $id) {
                            $ticket['title'] = $title;
                            $ticket['description'] = $description;
                            $ticket['status'] = $status;
                            break;
                        }
                    }
                } elseif ($action === 'delete' && $id > 0) {
                    $tickets = array_filter($tickets, fn($t) => $t['id'] != $id);
                }

                $_SESSION['tickify_tickets'] = array_values($tickets);  // Re-index
                $error = 'Ticket ' . ($action === 'delete' ? 'deleted' : ($action === 'update' ? 'updated' : 'created')) . ' successfully!';  // Sim toast
            }
        }

        echo $twig->render('tickets.twig', [
            'tickets' => $tickets,
            'form' => $form,
            'edit_mode' => $edit_mode,
            'edit_ticket' => $edit_ticket,
            'error' => $error ?? '',
            'title_error' => $title_error ?? '',
            'description_error' => $description_error ?? '',
            'status_error' => $status_error ?? ''
        ]);
        break;
        echo $twig->render('tickets.twig', [
            'tickets' => $_SESSION['tickify_tickets'] ?? [],
            'error' => $error ?? ''
        ]);
        break;
    default:
        echo $twig->render('landing.twig');  // 404 fallback to landing
}
?>