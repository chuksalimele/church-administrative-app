<?php

class Config {
    //main configuration
    public $server_host = "localhost";
    
    //database configuration
    public $db_host = "localhost";
    public $db_name = "churchapp_redeem";
    public $db_username = "churchapp";
    public $db_password = "churchapppass";
    
    //email configuration
    public $smtp_main_server = 'smtp.gmail.com';  // Specify main server
    public $smtp_servers = 'smtp.gmail.com;smtp.mail.yahoo.com';  // Specify main and backup SMTP servers
    public $smtp_username = 'chuksalimele@gmail.com';                 // SMTP username
    public $smtp_password = 'Kachukwu1234';                           // SMTP password
    
}