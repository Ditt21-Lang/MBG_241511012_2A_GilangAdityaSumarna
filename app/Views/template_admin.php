<!DOCTYPE html>
<html>
<head>
    <title><?= esc($title) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; padding:0; }
        .header { background:rgb(19, 70, 134); color:rgb(253, 244, 227); padding:15px; text-align:center; }
        .content { padding:20px; min-height:300px; }
        .footer { background:#333; color:#fff; text-align:center; padding:10px; }
    
        .nav-container {
            background-color: #ffffff;
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            gap: 1rem;
            justify-content: center;
        }

        @media (min-width: 640px) {
            .nav-container {
                flex-direction: row;
                gap: 1rem;
            }
        }

        .nav-link {
            width: 9rem; /* 144px */
            text-align: center;
            padding: 0.5rem 1rem;
            border-radius: 100px;
            border: 2px solid #3b82f6;
            color: #3b82f6;
            text-decoration: none;
            transition: all 0.3s ease-in-out;
            box-sizing: border-box; 
        }

        .nav-link:hover {
            background-color: #eff6ff;
            transform: scale(1.05);
        }

        .nav-link.active {
            background-color: rgb(237, 63, 39);
            color:rgb(253, 244, 227);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transform: scale(1.15);
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="header">
        <h2>Pengelolaan MBG: Admin</h2>
    </div>

    <div class="nav-container">
        <a href="<?= base_url('admin/home') ?>" class="nav-link"> Home </a>
        <a href="<?= base_url('admin/bahan_baku/add') ?>" class="nav-link"> Bahan Baku </a>
        <a href="<?= base_url('logout') ?>" class="nav-link"> Logout </a>
    </div>

    <div class="content">
        <?= $content ?>
    </div>

     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
     <script>
        document.addEventListener('DOMContentLoaded', () => {
            const navLinks = document.querySelectorAll('.nav-link');
            const currentPath = window.location.pathname;

            // Loop through all links and set the 'active' class on the matching one
            navLinks.forEach(link => {
                // Get the path from the link's href attribute
                const linkPath = new URL(link.href).pathname;

                // Check if the current path matches the link's path
                // The includes() method is used to handle potential query parameters or different file names
                if (currentPath.includes(linkPath) && linkPath !== "/") {
                    link.classList.add('active');
                }
            });
        });
     </script>
</body>
