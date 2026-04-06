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

/* base.html.twig */
class __TwigTemplate_dcbededd34da2bc1d5c17e9fe2a2c8ec extends Template
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

        $this->parent = false;

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'stylesheets' => [$this, 'block_stylesheets'],
            'javascripts' => [$this, 'block_javascripts'],
            'importmap' => [$this, 'block_importmap'],
            'body' => [$this, 'block_body'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "base.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "base.html.twig"));

        // line 1
        yield "<!DOCTYPE html>
<html>
    <head>
        <meta charset=\"UTF-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
        <title>";
        // line 6
        yield from $this->unwrap()->yieldBlock('title', $context, $blocks);
        yield "</title>
        ";
        // line 7
        yield from $this->unwrap()->yieldBlock('stylesheets', $context, $blocks);
        // line 17
        yield "
        ";
        // line 18
        yield from $this->unwrap()->yieldBlock('javascripts', $context, $blocks);
        // line 21
        yield "    </head>
    <body>
        <button id=\"nightModeToggle\" class=\"theme-toggle-btn btn btn-sm btn-outline-primary\" type=\"button\" aria-label=\"Enable night mode\" aria-pressed=\"false\">Night mode</button>
        ";
        // line 24
        $context["navUser"] = ((array_key_exists("currentUser", $context)) ? ((isset($context["currentUser"]) || array_key_exists("currentUser", $context) ? $context["currentUser"] : (function () { throw new RuntimeError('Variable "currentUser" does not exist.', 24, $this->source); })())) : (null));
        // line 25
        yield "        ";
        $context["navRoleLabel"] = ((((isset($context["navUser"]) || array_key_exists("navUser", $context) ? $context["navUser"] : (function () { throw new RuntimeError('Variable "navUser" does not exist.', 25, $this->source); })()) && CoreExtension::getAttribute($this->env, $this->source, ($context["navUser"] ?? null), "roleLabel", [], "any", true, true, false, 25))) ? (Twig\Extension\CoreExtension::lower($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, (isset($context["navUser"]) || array_key_exists("navUser", $context) ? $context["navUser"] : (function () { throw new RuntimeError('Variable "navUser" does not exist.', 25, $this->source); })()), "roleLabel", [], "any", false, false, false, 25))) : (""));
        // line 26
        yield "        ";
        $context["isAdminOffice"] = ((isset($context["navRoleLabel"]) || array_key_exists("navRoleLabel", $context) ? $context["navRoleLabel"] : (function () { throw new RuntimeError('Variable "navRoleLabel" does not exist.', 26, $this->source); })()) == "admin");
        // line 27
        yield "        ";
        $context["currentRoute"] = ((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["app"] ?? null), "request", [], "any", false, true, false, 27), "attributes", [], "any", false, true, false, 27), "get", ["_route"], "method", true, true, false, 27)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 27, $this->source); })()), "request", [], "any", false, false, false, 27), "attributes", [], "any", false, false, false, 27), "get", ["_route"], "method", false, false, false, 27), "")) : (""));
        // line 28
        yield "        ";
        if ((($tmp =  !(isset($context["isAdminOffice"]) || array_key_exists("isAdminOffice", $context) ? $context["isAdminOffice"] : (function () { throw new RuntimeError('Variable "isAdminOffice" does not exist.', 28, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 29
            yield "            ";
            $context["homeActive"] = CoreExtension::inFilter((isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 29, $this->source); })()), ["app_forum_feed", "app_forum_thread_detail", "app_thread_new", "app_thread_edit"]);
            // line 30
            yield "            ";
            $context["myActive"] = ((isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 30, $this->source); })()) == "app_forum_my_threads");
            // line 31
            yield "            ";
            $context["archivedActive"] = ((isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 31, $this->source); })()) == "app_forum_archived");
            // line 32
            yield "            ";
            $context["followedActive"] = ((isset($context["currentRoute"]) || array_key_exists("currentRoute", $context) ? $context["currentRoute"] : (function () { throw new RuntimeError('Variable "currentRoute" does not exist.', 32, $this->source); })()) == "app_forum_followed");
            // line 33
            yield "            <div class=\"container stats-shell pt-3\">
                <section class=\"card-soft front-nav-box p-3 d-flex flex-wrap align-items-center justify-content-between gap-2\">
                    <div>
                        <p class=\"mb-1 section-kicker text-uppercase\">Frontoffice</p>
                        <h2 class=\"h5 m-0\">Forum Navigation</h2>
                    </div>
                    <div class=\"d-flex flex-wrap gap-2\">
                        <a class=\"btn btn-sm ";
            // line 40
            yield (((($tmp = (isset($context["homeActive"]) || array_key_exists("homeActive", $context) ? $context["homeActive"] : (function () { throw new RuntimeError('Variable "homeActive" does not exist.', 40, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("btn-primary front-nav-btn is-active") : ("btn-outline-primary front-nav-btn"));
            yield "\" href=\"";
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_forum_feed");
            yield "\">Home</a>
                        <a class=\"btn btn-sm ";
            // line 41
            yield (((($tmp = (isset($context["myActive"]) || array_key_exists("myActive", $context) ? $context["myActive"] : (function () { throw new RuntimeError('Variable "myActive" does not exist.', 41, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("btn-primary front-nav-btn is-active") : ("btn-outline-primary front-nav-btn"));
            yield "\" href=\"";
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_forum_my_threads");
            yield "\">My Threads</a>
                        <a class=\"btn btn-sm ";
            // line 42
            yield (((($tmp = (isset($context["archivedActive"]) || array_key_exists("archivedActive", $context) ? $context["archivedActive"] : (function () { throw new RuntimeError('Variable "archivedActive" does not exist.', 42, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("btn-primary front-nav-btn is-active") : ("btn-outline-primary front-nav-btn"));
            yield "\" href=\"";
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_forum_archived");
            yield "\">Archived</a>
                        <a class=\"btn btn-sm ";
            // line 43
            yield (((($tmp = (isset($context["followedActive"]) || array_key_exists("followedActive", $context) ? $context["followedActive"] : (function () { throw new RuntimeError('Variable "followedActive" does not exist.', 43, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("btn-primary front-nav-btn is-active") : ("btn-outline-primary front-nav-btn"));
            yield "\" href=\"";
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_forum_followed");
            yield "\">Followed</a>
                    </div>
                </section>
            </div>
        ";
        }
        // line 48
        yield "        ";
        yield from $this->unwrap()->yieldBlock('body', $context, $blocks);
        // line 49
        yield "        ";
        yield $this->env->getRuntime('Symfony\Bridge\Twig\Extension\HttpKernelRuntime')->renderFragment($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_notifications_widget"));
        yield "
        <script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js\"></script>
        <script src=\"";
        // line 51
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("vendor/argon/js/argon-bootstrap5-bridge.js"), "html", null, true);
        yield "\"></script>
        <script>
            (function () {
                const bindNotificationWidget = function () {
                    const widget = document.getElementById('floatingNotify');
                    const button = document.getElementById('floatingNotifyBtn');

                    if (!widget || !button || button.dataset.bound === '1') {
                        return;
                    }

                    button.dataset.bound = '1';

                    const closePanel = function () {
                        widget.classList.remove('is-open');
                        button.setAttribute('aria-expanded', 'false');
                    };

                    const openPanel = function () {
                        widget.classList.add('is-open');
                        button.setAttribute('aria-expanded', 'true');
                    };

                    closePanel();

                    button.addEventListener('click', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        if (widget.classList.contains('is-open')) {
                            closePanel();
                            return;
                        }

                        openPanel();
                    });

                    document.addEventListener('click', function (event) {
                        if (widget.classList.contains('is-open') && !widget.contains(event.target)) {
                            closePanel();
                        }
                    });

                    document.addEventListener('keydown', function (event) {
                        if (event.key === 'Escape') {
                            closePanel();
                        }
                    });
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', bindNotificationWidget);
                } else {
                    bindNotificationWidget();
                }
            })();
        </script>
    </body>
</html>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 6
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

        yield "Serinity Web";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 7
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_stylesheets(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "stylesheets"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "stylesheets"));

        // line 8
        yield "            <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
            <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
            <link href=\"https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700\" rel=\"stylesheet\">
            <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css\" rel=\"stylesheet\">
            <link href=\"";
        // line 12
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("vendor/argon/css/nucleo-icons.css"), "html", null, true);
        yield "\" rel=\"stylesheet\">
            <link href=\"";
        // line 13
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("vendor/argon/css/nucleo-svg.css"), "html", null, true);
        yield "\" rel=\"stylesheet\">
            <link href=\"";
        // line 14
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("vendor/argon/css/font-awesome.css"), "html", null, true);
        yield "\" rel=\"stylesheet\">
            <link href=\"";
        // line 15
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("vendor/argon/css/argon-design-system.min.css"), "html", null, true);
        yield "\" rel=\"stylesheet\">
        ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 18
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_javascripts(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "javascripts"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "javascripts"));

        // line 19
        yield "            ";
        yield from $this->unwrap()->yieldBlock('importmap', $context, $blocks);
        // line 20
        yield "        ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 19
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_importmap(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "importmap"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "importmap"));

        yield $this->env->getRuntime('Symfony\Bridge\Twig\Extension\ImportMapRuntime')->importmap("app");
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 48
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

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "base.html.twig";
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
        return array (  336 => 48,  313 => 19,  302 => 20,  299 => 19,  286 => 18,  273 => 15,  269 => 14,  265 => 13,  261 => 12,  255 => 8,  242 => 7,  219 => 6,  148 => 51,  142 => 49,  139 => 48,  129 => 43,  123 => 42,  117 => 41,  111 => 40,  102 => 33,  99 => 32,  96 => 31,  93 => 30,  90 => 29,  87 => 28,  84 => 27,  81 => 26,  78 => 25,  76 => 24,  71 => 21,  69 => 18,  66 => 17,  64 => 7,  60 => 6,  53 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html>
    <head>
        <meta charset=\"UTF-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
        <title>{% block title %}Serinity Web{% endblock %}</title>
        {% block stylesheets %}
            <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
            <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
            <link href=\"https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700\" rel=\"stylesheet\">
            <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css\" rel=\"stylesheet\">
            <link href=\"{{ asset('vendor/argon/css/nucleo-icons.css') }}\" rel=\"stylesheet\">
            <link href=\"{{ asset('vendor/argon/css/nucleo-svg.css') }}\" rel=\"stylesheet\">
            <link href=\"{{ asset('vendor/argon/css/font-awesome.css') }}\" rel=\"stylesheet\">
            <link href=\"{{ asset('vendor/argon/css/argon-design-system.min.css') }}\" rel=\"stylesheet\">
        {% endblock %}

        {% block javascripts %}
            {% block importmap %}{{ importmap('app') }}{% endblock %}
        {% endblock %}
    </head>
    <body>
        <button id=\"nightModeToggle\" class=\"theme-toggle-btn btn btn-sm btn-outline-primary\" type=\"button\" aria-label=\"Enable night mode\" aria-pressed=\"false\">Night mode</button>
        {% set navUser = currentUser is defined ? currentUser : null %}
        {% set navRoleLabel = navUser and attribute(navUser, 'roleLabel') is defined ? navUser.roleLabel|lower : '' %}
        {% set isAdminOffice = navRoleLabel == 'admin' %}
        {% set currentRoute = app.request.attributes.get('_route')|default('') %}
        {% if not isAdminOffice %}
            {% set homeActive = currentRoute in ['app_forum_feed', 'app_forum_thread_detail', 'app_thread_new', 'app_thread_edit'] %}
            {% set myActive = currentRoute == 'app_forum_my_threads' %}
            {% set archivedActive = currentRoute == 'app_forum_archived' %}
            {% set followedActive = currentRoute == 'app_forum_followed' %}
            <div class=\"container stats-shell pt-3\">
                <section class=\"card-soft front-nav-box p-3 d-flex flex-wrap align-items-center justify-content-between gap-2\">
                    <div>
                        <p class=\"mb-1 section-kicker text-uppercase\">Frontoffice</p>
                        <h2 class=\"h5 m-0\">Forum Navigation</h2>
                    </div>
                    <div class=\"d-flex flex-wrap gap-2\">
                        <a class=\"btn btn-sm {{ homeActive ? 'btn-primary front-nav-btn is-active' : 'btn-outline-primary front-nav-btn' }}\" href=\"{{ path('app_forum_feed') }}\">Home</a>
                        <a class=\"btn btn-sm {{ myActive ? 'btn-primary front-nav-btn is-active' : 'btn-outline-primary front-nav-btn' }}\" href=\"{{ path('app_forum_my_threads') }}\">My Threads</a>
                        <a class=\"btn btn-sm {{ archivedActive ? 'btn-primary front-nav-btn is-active' : 'btn-outline-primary front-nav-btn' }}\" href=\"{{ path('app_forum_archived') }}\">Archived</a>
                        <a class=\"btn btn-sm {{ followedActive ? 'btn-primary front-nav-btn is-active' : 'btn-outline-primary front-nav-btn' }}\" href=\"{{ path('app_forum_followed') }}\">Followed</a>
                    </div>
                </section>
            </div>
        {% endif %}
        {% block body %}{% endblock %}
        {{ render(path('app_notifications_widget')) }}
        <script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js\"></script>
        <script src=\"{{ asset('vendor/argon/js/argon-bootstrap5-bridge.js') }}\"></script>
        <script>
            (function () {
                const bindNotificationWidget = function () {
                    const widget = document.getElementById('floatingNotify');
                    const button = document.getElementById('floatingNotifyBtn');

                    if (!widget || !button || button.dataset.bound === '1') {
                        return;
                    }

                    button.dataset.bound = '1';

                    const closePanel = function () {
                        widget.classList.remove('is-open');
                        button.setAttribute('aria-expanded', 'false');
                    };

                    const openPanel = function () {
                        widget.classList.add('is-open');
                        button.setAttribute('aria-expanded', 'true');
                    };

                    closePanel();

                    button.addEventListener('click', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        if (widget.classList.contains('is-open')) {
                            closePanel();
                            return;
                        }

                        openPanel();
                    });

                    document.addEventListener('click', function (event) {
                        if (widget.classList.contains('is-open') && !widget.contains(event.target)) {
                            closePanel();
                        }
                    });

                    document.addEventListener('keydown', function (event) {
                        if (event.key === 'Escape') {
                            closePanel();
                        }
                    });
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', bindNotificationWidget);
                } else {
                    bindNotificationWidget();
                }
            })();
        </script>
    </body>
</html>
", "base.html.twig", "C:\\Users\\saifd\\Documents\\serintiy\\serinity-web\\templates\\base.html.twig");
    }
}
