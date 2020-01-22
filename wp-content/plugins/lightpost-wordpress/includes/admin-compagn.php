<?php

if(!class_exists(Admin_Compagn)){
    
    class Admin_Compagn{
        
        function __construct() {
            
        }
            
        /*
        * Load Settings Page
        * @Since 1.0
        */
        function compagn_page(){
            ?>
            <h1>Compagns</h1>
            <table>
                <thead>
                    <tr>
                        <th>name</th>
                        <th>enable</th>
                        <th>actions</th>
                    </tr>
                </thead>
            </table>
            <?php
        }
    }
}
