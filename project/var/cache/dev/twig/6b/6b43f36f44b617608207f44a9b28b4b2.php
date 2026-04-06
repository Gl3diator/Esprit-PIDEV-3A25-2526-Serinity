<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* admin/index.html.twig */
class __TwigTemplate_493a8ec5dd79985053e7d4d0a2fcfded extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'body' => [$this, 'block_body'],
        ];
    }

    protected function doGetParent(array $context): bool|string|Template|TemplateWrapper
    {
        // line 1
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "admin/index.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "admin/index.html.twig"));

        $this->parent = $this->load("base.html.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 3
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        yield "Forum Backoffice";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 5
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_body(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        // line 6
        yield "<main class=\"page-shell\">
    <div class=\"container stats-shell\">
        <section class=\"card-soft p-3 p-md-4 mb-3 d-flex flex-wrap justify-content-between align-items-center gap-3\">
            <div>
                <p class=\"mb-1 section-kicker text-uppercase\">Administration</p>
                <h1 class=\"h4 m-0\">Forum Backoffice</h1>
            </div>
            <div class=\"d-flex gap-2\">
                <a class=\"btn btn-primary\" href=\"";
        // line 14
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_admin_category_new");
        yield "\">Add Category</a>
                <a class=\"btn btn-outline-primary\" href=\"";
        // line 15
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_admin_statistics");
        yield "\">Statistics</a>
            </div>
        </section>

        <div class=\"row g-3\">
            ";
        // line 20
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["categories"]) || array_key_exists("categories", $context) ? $context["categories"] : (function () { throw new RuntimeError('Variable "categories" does not exist.', 20, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["category"]) {
            // line 21
            yield "                <div class=\"col-md-6 col-lg-4\">
                    <article class=\"card-soft p-3 p-md-4 h-100 category-card\">
                        <div class=\"category-card-head mb-2\">
                            <h2 class=\"h6 mb-1\">";
            // line 24
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["category"], "name", [], "any", false, false, false, 24), "html", null, true);
            yield "</h2>
                            <p class=\"text-muted small mb-0\">/";
            // line 25
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["category"], "slug", [], "any", false, false, false, 25), "html", null, true);
            yield "</p>
                        </div>
                        <p class=\"mb-3\">";
            // line 27
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::slice($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["category"], "description", [], "any", false, false, false, 27), 0, 120), "html", null, true);
            yield "</p>
                        <div class=\"d-flex gap-2 mt-auto\">
                            <a class=\"btn btn-sm btn-outline-secondary\" href=\"";
            // line 29
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_admin_category_edit", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["category"], "id", [], "any", false, false, false, 29)]), "html", null, true);
            yield "\">Edit</a>
                            <form method=\"post\" action=\"";
            // line 30
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_admin_category_delete", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["category"], "id", [], "any", false, false, false, 30)]), "html", null, true);
            yield "\">
                                <button class=\"btn btn-sm btn-outline-danger\">Delete</button>
                            </form>
                        </div>
                    </article>
                </div>
            ";
            $context['_iterated'] = true;
        }
        // line 36
        if (!$context['_iterated']) {
            // line 37
            yield "                <div class=\"col-12\">
                    <div class=\"card-soft p-4 text-muted\">No categories yet.</div>
                </div>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['category'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 41
        yield "        </div>
    </div>
</main>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "admin/index.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  172 => 41,  163 => 37,  161 => 36,  150 => 30,  146 => 29,  141 => 27,  136 => 25,  132 => 24,  127 => 21,  122 => 20,  114 => 15,  110 => 14,  100 => 6,  87 => 5,  64 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}Forum Backoffice{% endblock %}

{% block body %}
<main class=\"page-shell\">
    <div class=\"container stats-shell\">
        <section class=\"card-soft p-3 p-md-4 mb-3 d-flex flex-wrap justify-content-between align-items-center gap-3\">
            <div>
                <p class=\"mb-1 section-kicker text-uppercase\">Administration</p>
                <h1 class=\"h4 m-0\">Forum Backoffice</h1>
            </div>
            <div class=\"d-flex gap-2\">
                <a class=\"btn btn-primary\" href=\"{{ path('app_admin_category_new') }}\">Add Category</a>
                <a class=\"btn btn-outline-primary\" href=\"{{ path('app_admin_statistics') }}\">Statistics</a>
            </div>
        </section>

        <div class=\"row g-3\">
            {% for category in categories %}
                <div class=\"col-md-6 col-lg-4\">
                    <article class=\"card-soft p-3 p-md-4 h-100 category-card\">
                        <div class=\"category-card-head mb-2\">
                            <h2 class=\"h6 mb-1\">{{ category.name }}</h2>
                            <p class=\"text-muted small mb-0\">/{{ category.slug }}</p>
                        </div>
                        <p class=\"mb-3\">{{ category.description|slice(0, 120) }}</p>
                        <div class=\"d-flex gap-2 mt-auto\">
                            <a class=\"btn btn-sm btn-outline-secondary\" href=\"{{ path('app_admin_category_edit', {id: category.id}) }}\">Edit</a>
                            <form method=\"post\" action=\"{{ path('app_admin_category_delete', {id: category.id}) }}\">
                                <button class=\"btn btn-sm btn-outline-danger\">Delete</button>
                            </form>
                        </div>
                    </article>
                </div>
            {% else %}
                <div class=\"col-12\">
                    <div class=\"card-soft p-4 text-muted\">No categories yet.</div>
                </div>
            {% endfor %}
        </div>
    </div>
</main>
{% endblock %}
", "admin/index.html.twig", "C:\\Users\\saifd\\Documents\\serintiy\\serinity-web\\templates\\admin\\index.html.twig");
    }
}
