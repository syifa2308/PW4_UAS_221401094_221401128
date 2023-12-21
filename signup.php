<?php

require 'function.php';



// Cek sudah login atau belum
if (isset($_POST['signup'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    
    // Simpan data ke dalam tabel user
    $insertQuery = "INSERT INTO user (username, email, password) VALUES ('$username', '$email', '$password')";
    $result = mysqli_query($conn, $insertQuery);


    if ($result) {
        // Registrasi sukses, redirect ke halaman home
        header('location:home.php');
        exit();
    } else {
        // Gagal menyimpan data
        $error_message = 'Gagal mendaftar. Silakan coba lagi.';
    }
}


?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Sign Up</title>
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-primary">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Sign Up</h3></div>
                                    <div class="card-body">
                                        <form method="POST" action="signup.php">
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="Username" name="username" type="text" placeholder="Username" />
                                                <label for="Username" >Username</label>
                                            </div>

                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="Email" name="email" type="email" placeholder="name@example.com" />
                                                <label for="Email" >Email address</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="Password" name="password"  type="password" placeholder="Password" />
                                                <label for="Password" >Password</label>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <button class="btn btn-primary"  name="signup" >Sign Up</button>
                                            </div>
                                            
                                        </form>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>
