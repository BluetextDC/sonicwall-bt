Feature: Test the CustomCSSandJS_ShowCodesPro class

    Scenario: Test the `url_rules` function 
        Given the following url rules:
            | type          | value   | codes  |
            | all           |         | 1.css  |
            | first-page    |         | 2.css  |
            | contains      | aa      | 3.css  |
            | equals-to     | bb      | 4.css  |
            | begins-with   | /cc     | 5.css  |
            | ends-by       | dd      | 6.css  |
            | not-contains  | ee      | 7.css  |
            | not-contains  | gg      | 1.css  |
            | not-equal-to  | ff      | 8.css  |
            | not-equal-to  | ii      | 1.css  |

        Then the url_rules function returns:
            | url           | result                |
            | /             | 1.css, 2.css          |
            | /ccaa         | 1.css, 3.css, 5.css   |
            | /ccee         | 1.css, 5.css          |
            | /ccaabbdd     | 1.css, 3.css, 5.css, 6.css |
            | ff            | 1.css                 |
            | ii            |                       |
            | /ccgg         | 5.css                 |


    @to-do
    Scenario: Test the `filter_search_tree` function  
        Given "1.css, 2.css, 3.css" allowed urls
        And the following search_tree:
            | type                         | codes              |
            | frontend-js-header-external  | 30.js?v=7361, 30.js?v=7361 |
            | frontend-css-header-external | 1.css?v=1111, 2.css?v=5678, 6.css?v=2932 |
            | frontend-css-header-internal | 2.css, 3.css, 7.css |
        Then the filter_search_tree function returns:
            | type                         | codes              |
            | frontend-css-header-external | 1.css?v=1111, 2.css?v=5678 |
            | frontend-css-header-internal | 2.css, 3.css |

    Scenario: Test 1 for the LESS preprocessor (first-page example) 
        Given the following input:
        """
        @base: #f938ab;

        .box-shadow(@style, @c) when (iscolor(@c)) {
          -webkit-box-shadow: @style @c;
          box-shadow:         @style @c;
        }
        .box-shadow(@style, @alpha: 50%) when (isnumber(@alpha)) {
          .box-shadow(@style, rgba(0, 0, 0, @alpha));
        }
        .box {
          color: saturate(@base, 5%);
          border-color: lighten(@base, 30%);
          div { .box-shadow(0 0 5px, 30%) }
        }
        """
        Then the LESS processor class returns:
        """
        .box {
          color: #fe33ac;
          border-color: #fdcdea;
        }
        .box div {
          -webkit-box-shadow: 0 0 5px rgba(0,0,0,0.3);
          box-shadow: 0 0 5px rgba(0,0,0,0.3);
        }
        """


    Scenario: Test 2 for the LESS preprocessor (Nested rules) 
        Given the following input:
        """
        #header {
          color: black;
          .navigation {
            font-size: 12px;
          }
          .logo {
            width: 300px;
          }
        }
        """
        Then the LESS processor class returns:
        """
        #header {
          color: black;
        }
        #header .navigation {
          font-size: 12px;
        }
        #header .logo {
          width: 300px;
        }
        """


    Scenario: Test 1 for the SASS preprocessor (Only SCSS style) (Nested rules) 
        Given the following input:
        """
        nav {
          ul {
            margin: 0;
            padding: 0;
            list-style: none;
          }

          li { display: inline-block; }

          a {
            display: block;
            padding: 6px 12px;
            text-decoration: none;
          }
        }
        """
        Then the SASS processor class returns:
        """
        nav ul {
          margin: 0;
          padding: 0;
          list-style: none; }
          nav li {
            display: inline-block; }
          nav a {
            display: block;
            padding: 6px 12px;
            text-decoration: none; }
        """

    Scenario: Test JavaScript minification
        For now the https://github.com/tedious/JShrink library is used
        Alternative the https://github.com/rgrove/jsmin-php can be used
        The best results should come with the http://closure-compiler.appspot.com/home , but I need to implement their REST API
        Given the following input:
        """
        $.ajax({
          url: "/api/getWeather",
          data: {
            zipcode: 97201
          },
          success: function( result ) {
            $( "#weather-temp" ).html( "<strong>" + result + "</strong> degrees" );
          }
        });
        """
        Then the JS minifier class returns:
        """
        $.ajax({url:"/api/getWeather",data:{zipcode:97201},success:function(result){$("#weather-temp").html("<strong>"+result+"</strong> degrees");}});
        """


    Scenario: Test the CSS minification
        The https://github.com/tubalmartin/YUI-CSS-compressor-PHP-port library is used
        Given the following input:
        """
        nav ul {
            margin: 0;
            padding: 0;
            list-style: none; 
            color: #ff0000;
        }
        nav li {
            display: inline-block; 
        }
        nav a {
            display: block;
            padding: 12px 12px;
            text-decoration: none; 
        }
        """
        Then the CSS minifier class returns:
        """
        nav ul{margin:0;padding:0;list-style:none;color:red}nav li{display:inline-block}nav a{display:block;padding:12px 12px;text-decoration:none}
        """


    @to-do
    Scenario: Test a Less preprocessing and afterwards a minification
        Given the following input:

        """
        #header {
          color: black;
          .navigation {
            font-size: 12px;
          }
          .logo {
            width: 300px;
          }
        }
        """
        Then the LESS preprocessor and minifier class returns:
        """
        I don't know
        """
