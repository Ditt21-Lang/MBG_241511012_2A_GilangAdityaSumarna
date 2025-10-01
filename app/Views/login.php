<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .menu{
            background-color: rgb(19, 70, 134)
        }

        .form{
            background-color: rgb(253, 244, 227);
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center menu" style="height:100vh;">
    <div class="card shadow-lg p-4 form" style="width: 350px;">
        <h3 class="fw-bold text-center mb-5">Login </h3>    

        <?php if (session()->getFlashdata('error')):?>
            <div id="flashError" class="alert alert-danger text-center"   role="alert">
            <p><?= session()->getFlashdata('error') ?></p>
            </div>
        <?php endif; ?>
        
        <div id="alertMessage" class="alert d-none" role="alert"></div>

        <form action="<?= base_url('auth/processLogin') ?>" method="post" id="loginForm">
            <div class="mb-3">
                <label  class="form-label">Email:</label>
                <input type="text" name="email" class="form-control" required >
                 <div class="text-danger mt-1"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Password:</label>
                <input type="password" name="password"  class="form-control" required >
                <div class="text-danger mt-1"></div>
            </div>

            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary wb">Login</button>
            </div>
        </form>
    </div>    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        const flashError = document.getElementById('flashError');
        if(flashError) {
            setTimeout (() => {
                flashError.style.transition = "opacity 0.5s ease";
                flashError.style.opacity = 0;

                setTimeout (() => {
                    flashError.remove();
                }, 500);
            }, 3000);
        }
    </script>
</body>
</html>