<?php

/* tooltip.twig */
class __TwigTemplate_2b7da06fa3282b55f5df255d4506c1ad94fa3a74b1af29336936e23261172094 extends Twig_Template
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
        echo "<a href=\"#\" class=\"js-wpml-ls-tooltip-open wpml-ls-tooltip-open otgs-ico-help\" data-content=\"";
        echo twig_escape_filter($this->env, $this->getAttribute(($context["content"] ?? null), "text", array()), "html_attr");
        echo "\" data-link-text=\"";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["content"] ?? null), "link", array()), "text", array()), "html_attr");
        echo "\" data-link-url=\"";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["content"] ?? null), "link", array()), "url", array()), "html_attr");
        echo "\" data-link-target=\"";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["content"] ?? null), "link", array()), "target", array()), "html_attr");
        echo "\"></a>";
    }

    public function getTemplateName()
    {
        return "tooltip.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "tooltip.twig", "C:\\OSPanel\\domains\\testing\\wp-content\\plugins\\sitepress-multilingual-cms\\templates\\language-switcher-admin-ui\\tooltip.twig");
    }
}
