<?php
session_start();

// Jika sudah login, langsung redirect ke dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: index.php");
    exit();
}

include 'config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "Username dan password tidak boleh kosong.";
    } else {
        $stmt = $conn->prepare("SELECT id, password FROM admin_users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($db_id, $db_password);
                $stmt->fetch();
                if (password_verify($password, $db_password)) {
                    $_SESSION['logged_in'] = true;
                    $_SESSION['username'] = $username;
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Password salah. Silakan coba lagi.";
                }
            } else {
                $error = "Username tidak ditemukan.";
            }
            $stmt->close();
        } else {
            $error = "Terjadi kesalahan sistem. Hubungi administrator.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Hub - Pusat Data Alumni</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.95)), url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') no-repeat center center fixed;
            background-size: cover;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(16px);
            padding: 44px 40px;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-logo .icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #4338ca, #3b82f6);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 16px;
            color: #ffffff;
            box-shadow: 0 10px 15px -3px rgba(67, 56, 202, 0.3);
        }

        .login-logo h1 {
            font-size: 1.4rem;
            color: #0f172a;
            font-weight: 700;
            margin-bottom: 6px;
            letter-spacing: -0.02em;
        }

        .login-logo p {
            font-size: 0.9rem;
            color: #64748b;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.9rem;
            color: #334155;
            margin-bottom: 8px;
        }

        .input-icon-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon-wrapper i {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            font-size: 1rem;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px 12px 42px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
            box-sizing: border-box;
            background: #f8fafc;
        }

        .form-group input:focus {
            outline: none;
            border-color: #4338ca;
            background: white;
            box-shadow: 0 0 0 3px rgba(67, 56, 202, 0.15);
        }
        
        .form-group input:focus + i, .input-icon-wrapper input:focus ~ i {
            color: #4338ca;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4338ca, #3b82f6);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
            margin-top: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(67, 56, 202, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .login-footer {
            text-align: center;
            margin-top: 32px;
            font-size: 0.8rem;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
        }

        .default-creds {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 0.85rem;
            color: #0369a1;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-logo">
                <div class="icon"><i class="fas fa-university"></i></div>
                <h1>Pangkalan Data Alumni</h1>
                <p>Otentikasi Portal Administrator</p>
            </div>

            <?php if ($error != ""): ?>
                <div class="alert-error"><i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST" autocomplete="off">
                <div class="form-group">
                    <label for="username">Username Akses</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username"
                            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                            placeholder="Ketikkan username Anda" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Ketikkan kata sandi Anda" required>
                    </div>
                </div>

                <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> Masuk ke Sistem</button>
            </form>

            <div class="login-footer">
                &copy; <?php echo date('Y'); ?> Hak Cipta Dilindungi.<br>Pusat Data & Informasi Alumni.
            </div>
        </div>
    </div>
</body>

</html>
