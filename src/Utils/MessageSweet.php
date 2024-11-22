<?php

namespace App\Utils;

class MessageSweet
{
    public static function displaySweetAlertMessage()
    {
        if ($message = $_SESSION["message"] ?? false) {
            echo <<< TXT
                <script>
                    Swal.fire({
                      position: "center",
                      icon: "success",
                      title: "$message",
                      showConfirmButton: false,
                      timer: 2000
                    });                
                </script>
            TXT;
            unset($_SESSION["message"]);
        }
    }
}
