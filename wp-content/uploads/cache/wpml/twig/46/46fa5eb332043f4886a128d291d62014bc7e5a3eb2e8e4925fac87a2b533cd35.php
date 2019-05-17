<?php

/* reset-pro-trans-config.twig */
class __TwigTemplate_b5fbee0543e4a72115034b6e05755df6ba3fd486afd7a7f8fc0421fa03689e37 extends Twig_Template
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
        echo "<div class=\"icl_cyan_box\" id=\"";
        echo twig_escape_filter($this->env, ($context["placeHolder"] ?? null));
        echo "\">
\t<h3>";
        // line 2
        echo twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "title", array()), "html", null, true);
        echo "</h3>

\t<div class=\"icl_form_errors\">
\t\t";
        // line 5
        echo twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "alert1", array()), "html", null, true);
        echo "
\t\t<br/>
\t\t";
        // line 7
        echo twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "alert2", array()), "html", null, true);
        echo "
\t</div>
\t<p style=\"padding:6px;\">
\t\t<input id=\"icl_reset_pro_check\" type=\"checkbox\" value=\"1\"/>
\t\t&nbsp;<label for=\"icl_reset_pro_check\">";
        // line 11
        echo twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "checkBoxLabel", array()), "html", null, true);
        echo "</label>
\t</p>

\t<p style=\"padding:6px;\">
\t\t<a id=\"icl_reset_pro_but\" href=\"#\" class=\"button-primary button-primary-disabled\">
\t\t\t";
        // line 16
        echo twig_escape_filter($this->env, $this->getAttribute(($context["strings"] ?? null), "button", array()), "html", null, true);
        echo "
\t\t</a><span class=\"spinner\"></span>
\t</p>

</div>
<br clear=\"all\"/>";
    }

    public function getTemplateName()
    {
        return "reset-pro-trans-config.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  50 => 16,  42 => 11,  35 => 7,  30 => 5,  24 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "reset-pro-trans-config.twig", "/home/186477.cloudwaysapps.com/wbwuhycqkv/public_html/wp-content/plugins/wpml-translation-management/templates/troubleshooting/reset-pro-trans-config.twig");
    }
}
