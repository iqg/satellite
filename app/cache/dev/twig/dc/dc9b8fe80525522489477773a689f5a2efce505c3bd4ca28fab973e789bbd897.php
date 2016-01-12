<?php

/* @Twig/Exception/exception_full.html.twig */
class __TwigTemplate_f47af4d1acb7f7c55d17cc6933320dbe4adc05ff570508ebbac82f82856d1ae3 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("@Twig/layout.html.twig", "@Twig/Exception/exception_full.html.twig", 1);
        $this->blocks = array(
            'head' => array($this, 'block_head'),
            'title' => array($this, 'block_title'),
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@Twig/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_a87f187ccbbfc0ee025113f636f8fe85b4873825cf7cb08b008c09daa45624a8 = $this->env->getExtension("native_profiler");
        $__internal_a87f187ccbbfc0ee025113f636f8fe85b4873825cf7cb08b008c09daa45624a8->enter($__internal_a87f187ccbbfc0ee025113f636f8fe85b4873825cf7cb08b008c09daa45624a8_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@Twig/Exception/exception_full.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_a87f187ccbbfc0ee025113f636f8fe85b4873825cf7cb08b008c09daa45624a8->leave($__internal_a87f187ccbbfc0ee025113f636f8fe85b4873825cf7cb08b008c09daa45624a8_prof);

    }

    // line 3
    public function block_head($context, array $blocks = array())
    {
        $__internal_d10f6c72a3e8993b0c93cef8a3b6808aacfc64990dc02c90549d0b4f61094bf1 = $this->env->getExtension("native_profiler");
        $__internal_d10f6c72a3e8993b0c93cef8a3b6808aacfc64990dc02c90549d0b4f61094bf1->enter($__internal_d10f6c72a3e8993b0c93cef8a3b6808aacfc64990dc02c90549d0b4f61094bf1_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "head"));

        // line 4
        echo "    <link href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('request')->generateAbsoluteUrl($this->env->getExtension('asset')->getAssetUrl("bundles/framework/css/exception.css")), "html", null, true);
        echo "\" rel=\"stylesheet\" type=\"text/css\" media=\"all\" />
";
        
        $__internal_d10f6c72a3e8993b0c93cef8a3b6808aacfc64990dc02c90549d0b4f61094bf1->leave($__internal_d10f6c72a3e8993b0c93cef8a3b6808aacfc64990dc02c90549d0b4f61094bf1_prof);

    }

    // line 7
    public function block_title($context, array $blocks = array())
    {
        $__internal_c9ea936c3805b829de089ebe90539fbf04abc20db0397959a2c697736eb196ff = $this->env->getExtension("native_profiler");
        $__internal_c9ea936c3805b829de089ebe90539fbf04abc20db0397959a2c697736eb196ff->enter($__internal_c9ea936c3805b829de089ebe90539fbf04abc20db0397959a2c697736eb196ff_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "title"));

        // line 8
        echo "    ";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["exception"]) ? $context["exception"] : $this->getContext($context, "exception")), "message", array()), "html", null, true);
        echo " (";
        echo twig_escape_filter($this->env, (isset($context["status_code"]) ? $context["status_code"] : $this->getContext($context, "status_code")), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, (isset($context["status_text"]) ? $context["status_text"] : $this->getContext($context, "status_text")), "html", null, true);
        echo ")
";
        
        $__internal_c9ea936c3805b829de089ebe90539fbf04abc20db0397959a2c697736eb196ff->leave($__internal_c9ea936c3805b829de089ebe90539fbf04abc20db0397959a2c697736eb196ff_prof);

    }

    // line 11
    public function block_body($context, array $blocks = array())
    {
        $__internal_ddb36f7d57b87378fed6966d6dde345e5a9e8e91b6d5837a7e7a9689cec483e9 = $this->env->getExtension("native_profiler");
        $__internal_ddb36f7d57b87378fed6966d6dde345e5a9e8e91b6d5837a7e7a9689cec483e9->enter($__internal_ddb36f7d57b87378fed6966d6dde345e5a9e8e91b6d5837a7e7a9689cec483e9_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 12
        echo "    ";
        $this->loadTemplate("@Twig/Exception/exception.html.twig", "@Twig/Exception/exception_full.html.twig", 12)->display($context);
        
        $__internal_ddb36f7d57b87378fed6966d6dde345e5a9e8e91b6d5837a7e7a9689cec483e9->leave($__internal_ddb36f7d57b87378fed6966d6dde345e5a9e8e91b6d5837a7e7a9689cec483e9_prof);

    }

    public function getTemplateName()
    {
        return "@Twig/Exception/exception_full.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  78 => 12,  72 => 11,  58 => 8,  52 => 7,  42 => 4,  36 => 3,  11 => 1,);
    }
}
/* {% extends '@Twig/layout.html.twig' %}*/
/* */
/* {% block head %}*/
/*     <link href="{{ absolute_url(asset('bundles/framework/css/exception.css')) }}" rel="stylesheet" type="text/css" media="all" />*/
/* {% endblock %}*/
/* */
/* {% block title %}*/
/*     {{ exception.message }} ({{ status_code }} {{ status_text }})*/
/* {% endblock %}*/
/* */
/* {% block body %}*/
/*     {% include '@Twig/Exception/exception.html.twig' %}*/
/* {% endblock %}*/
/* */
