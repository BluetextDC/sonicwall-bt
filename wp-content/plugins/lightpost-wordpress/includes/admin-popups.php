<?php

if(!class_exists(Admin_Popups)){
    
    class Admin_Popups{
        
        function __construct() {
            
        }
            
            /*
            * Load Popups List
            * @Since 1.0
            */
            function popups_list_page(){
                ?>
                <h1>List popups</h1>
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
            
            function t(){
                
            }
    }
}
