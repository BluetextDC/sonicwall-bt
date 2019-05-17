<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

define( 'ABSPATH', '/var/www' );
require_once 'includes/class-show-codes.php';

/**
 * Defines application features from the specific context.
 */
// class FeatureContext implements Context, SnippetAcceptingContext
class FeatureContext implements Context, SnippetAcceptingContext
{

    var $class;
    var $url_rules = array();
    var $allowed_urls;
    var $search_tree;
    var $input;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->class = new CustomCSSandJS_ShowCodesPro();
        $this->class->set_value('first_page', '/');
    }

    /**
     * @Given the following url rules:
     */
    public function theFollowingUrlRules(TableNode $table)
    {
        $rules = array();

        foreach( $table as $_value ) {
            $rules_array = array();
            if ( empty( trim( $_value['value'] ) ) ) {
                $rules_array = explode(',', $_value['codes'] );
                $rules_array = array_map( 'trim', $rules_array );
            } else {
                $codes = explode( ',', $_value['codes'] );
                $codes = array_map('trim', $codes);
                $rules_array[trim($_value['value'])] = $codes;
            }
            $rules[$_value['type']] = $rules_array;
        }

        $this->url_rules = $rules;
    }

    /**
     * @Then the url_rules function returns:
     */
    public function theUrlRulesFunctionReturns(TableNode $table)
    {
        foreach( $table as $_value ) {
           $result = $this->class->url_rules( $_value['url'], $this->url_rules ); 
           asort($result);

           if ( implode(', ', $result ) != $_value['result'] ) {
               throw new Exception( 'Wrong result: '. PHP_EOL.
                   'Expected: ' . $_value['result'] . PHP_EOL . 
                    'Got: ' . implode(', ', $result ));
           }
        }
    }

    /**
     * @Given :arg1 allowed urls
     */
    public function allowedUrls($arg1)
    {
        $arg1 = explode(',', $arg1 );
        $this->allowed_urls = array_map('trim', $arg1 ); 
    }

    /**
     * @Given the following search_tree:
     */
    public function theFollowingSearchTree(TableNode $table)
    {
        $rules = array();

        foreach( $table as $_value ) {
            $rules_array = explode(',', $_value['codes'] );
            $rules_array = array_map( 'trim', $rules_array );
            $rules[$_value['type']] = $rules_array;
        }

        $this->search_tree = $rules;
    }

    /**
     * @Then the filter_search_tree function returns:
     */
    public function theFilterSearchTreeFunctionReturns(TableNode $table)
    {

        $rules = array();

        foreach( $table as $_value ) {
            $rules_array = explode(',', $_value['codes'] );
            $rules_array = array_map( 'trim', $rules_array );
            $rules[$_value['type']] = $rules_array;
        }

        $this->search_tree = $rules;

        throw new PendingException();
    }

    /**
     * @Given the following input:
     */
    public function theFollowingInput(PyStringNode $string)
    {
        $this->input = $string->getRaw();
    }

    /**
     * @Then the :arg1 processor class returns:
     */
    public function theProcessorClassReturns($arg1, PyStringNode $string)
    {
        $preprocessor = strtolower($arg1);

        if ( $preprocessor == 'less' ) {
            require_once( 'includes/vendor/lessc.inc.php' );
            $compiler = new lessc();
        }

        if ( $preprocessor == 'sass' ) {
            require_once( 'includes/vendor/scss.inc.php' );
            $compiler = new scssc();
        }


        $output = '';
        if ( isset( $compiler ) ) {
            try {
                $output = $compiler->compile( $this->input );
            } catch( Exception $e ) {
                $output = $this->input;
            }
        }

        if ( $output != $string->getRaw() . "\n" ) {
            throw new Exception("Wrong preprocessing:" . PHP_EOL . "Result was: " . $output);
        }
    }

    /**
     * @Then the :arg1 minifier class returns:
     */
    public function theMinifierClassReturns($arg1, PyStringNode $string)
    {

        $content = '';
        $language = strtolower( $arg1 );
        if ( $language == 'css' ) {
            include_once( 'includes/vendor/CSSmin.php' ); 
            $obj = new CSSmin();
            $content = $obj->run( $this->input);

        }

        if ( $language == 'js' ) {
            include_once( 'includes/vendor/Minifier.php' );
            $content = Minifier::minify( $this->input );
        }

        if ( $content != $string->getRaw() ) {
            throw new Exception("Wrong minification:" . PHP_EOL . "Result was: " . $content );
        }
    }
}
