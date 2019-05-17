<?php

/* st-db-cache-tables.twig */
class __TwigTemplate_4123a173f60bfaf40f6188d291fbf3fea018817a0dcff6e29f22733f1354deae extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<p>
    <input id=\"icl_run_st_db_cache_command\"
           type=\"button\" class=\"button-secondary\"
           value=\"";
        // line 4
        echo twig_escape_filter($this->env, ($context["buttonLabel"] ?? null), "html", null, true);
        echo "\"
           data-success-message=\"";
        // line 5
        echo twig_escape_filter($this->env, ($context["successMsg"] ?? null), "html", null, true);
        echo "\"
           data-nonce=\"";
        // line 6
        echo twig_escape_filter($this->env, ($context["nonce"] ?? null), "html", null, true);
        echo "\"
    />
    <br/>
    <small style=\"margin-left:10px;\">";
        // line 9
        echo twig_escape_filter($this->env, ($context["description"] ?? null), "html", null, true);
        echo "</small>
</p>";
    }

    public function getTemplateName()
    {
        return "st-db-cache-tables.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  38 => 9,  32 => 6,  28 => 5,  24 => 4,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "st-db-cache-tables.twig", "/home/186477.cloudwaysapps.com/wbwuhycqkv/public_html/wp-content/plugins/wpml-string-translation/templates/troubleshooting/st-db-cache-tables.twig");
    }
}
