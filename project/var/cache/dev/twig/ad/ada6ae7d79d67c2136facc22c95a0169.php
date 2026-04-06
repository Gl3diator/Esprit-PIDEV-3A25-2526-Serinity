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

/* forum/feed.html.twig */
class __TwigTemplate_f62444d18cf3a51745a3ae0ecc1ce6b4 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "forum/feed.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "forum/feed.html.twig"));

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

        yield "Forum Feed";
        
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
        <section class=\"hero-shell hero-shell-forum mb-4 p-4 p-md-5\">
            <div class=\"d-flex flex-wrap justify-content-between align-items-center gap-3\">
                <div>
                    <p class=\"text-uppercase fw-bold mb-1 small text-primary section-kicker\">Serinity Community</p>
                    <h1 class=\"display-6 fw-bold mb-2\">Forum Posts</h1>
                    <p class=\"m-0 text-muted\">Share questions, support others, and track meaningful conversations.</p>
                </div>
                <div class=\"stats-chip-wrap\">
                    <span class=\"stats-chip\">Threads: ";
        // line 16
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::length($this->env->getCharset(), (isset($context["threads"]) || array_key_exists("threads", $context) ? $context["threads"] : (function () { throw new RuntimeError('Variable "threads" does not exist.', 16, $this->source); })())), "html", null, true);
        yield "</span>
                    <span class=\"stats-chip\">User: ";
        // line 17
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["currentUser"]) || array_key_exists("currentUser", $context) ? $context["currentUser"] : (function () { throw new RuntimeError('Variable "currentUser" does not exist.', 17, $this->source); })()), "username", [], "any", false, false, false, 17), "html", null, true);
        yield "</span>
                </div>
            </div>
        </section>

        <section class=\"card-soft action-strip p-3 mb-3 d-flex flex-wrap gap-2 justify-content-between align-items-center\">
            <h2 class=\"h4 m-0\">Browse Discussions</h2>
            <a class=\"btn btn-primary btn-icon\" href=\"";
        // line 24
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_thread_new");
        yield "\">
                <span class=\"btn-inner--icon\"><i class=\"ni ni-fat-add\"></i></span>
                <span class=\"btn-inner--text\">New Thread</span>
            </a>
        </section>

        <form method=\"get\" class=\"card-soft filter-strip p-3 mb-4 row g-2 align-items-end\" id=\"feedFilterForm\">
            <div class=\"col-lg-6 col-md-6\">
                <label class=\"form-label\">Search</label>
                <div class=\"d-flex gap-2\">
                    <input type=\"text\" name=\"q\" value=\"";
        // line 34
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 34, $this->source); })()), "request", [], "any", false, false, false, 34), "query", [], "any", false, false, false, 34), "get", ["q"], "method", false, false, false, 34), "html", null, true);
        yield "\" class=\"form-control\" placeholder=\"Search title...\">
                    <button class=\"btn btn-primary px-3\" type=\"submit\">Search</button>
                </div>
            </div>
            <div class=\"col-lg-4 col-md-4\">
                <label class=\"form-label\">Sort by</label>
                <select name=\"sort\" class=\"form-select\" onchange=\"this.form.requestSubmit()\">
                    <option value=\"newest\" ";
        // line 41
        yield ((((isset($context["currentSort"]) || array_key_exists("currentSort", $context) ? $context["currentSort"] : (function () { throw new RuntimeError('Variable "currentSort" does not exist.', 41, $this->source); })()) == "newest")) ? ("selected") : (""));
        yield ">Newest First</option>
                    <option value=\"oldest\" ";
        // line 42
        yield ((((isset($context["currentSort"]) || array_key_exists("currentSort", $context) ? $context["currentSort"] : (function () { throw new RuntimeError('Variable "currentSort" does not exist.', 42, $this->source); })()) == "oldest")) ? ("selected") : (""));
        yield ">Oldest First</option>
                    <option value=\"most_points\" ";
        // line 43
        yield ((((isset($context["currentSort"]) || array_key_exists("currentSort", $context) ? $context["currentSort"] : (function () { throw new RuntimeError('Variable "currentSort" does not exist.', 43, $this->source); })()) == "most_points")) ? ("selected") : (""));
        yield ">Most Points</option>
                    <option value=\"least_points\" ";
        // line 44
        yield ((((isset($context["currentSort"]) || array_key_exists("currentSort", $context) ? $context["currentSort"] : (function () { throw new RuntimeError('Variable "currentSort" does not exist.', 44, $this->source); })()) == "least_points")) ? ("selected") : (""));
        yield ">Least Points</option>
                    <option value=\"most_comments\" ";
        // line 45
        yield ((((isset($context["currentSort"]) || array_key_exists("currentSort", $context) ? $context["currentSort"] : (function () { throw new RuntimeError('Variable "currentSort" does not exist.', 45, $this->source); })()) == "most_comments")) ? ("selected") : (""));
        yield ">Most Comments</option>
                    <option value=\"least_comments\" ";
        // line 46
        yield ((((isset($context["currentSort"]) || array_key_exists("currentSort", $context) ? $context["currentSort"] : (function () { throw new RuntimeError('Variable "currentSort" does not exist.', 46, $this->source); })()) == "least_comments")) ? ("selected") : (""));
        yield ">Least Comments</option>
                    <option value=\"most_followers\" ";
        // line 47
        yield ((((isset($context["currentSort"]) || array_key_exists("currentSort", $context) ? $context["currentSort"] : (function () { throw new RuntimeError('Variable "currentSort" does not exist.', 47, $this->source); })()) == "most_followers")) ? ("selected") : (""));
        yield ">Most Followers</option>
                    <option value=\"least_followers\" ";
        // line 48
        yield ((((isset($context["currentSort"]) || array_key_exists("currentSort", $context) ? $context["currentSort"] : (function () { throw new RuntimeError('Variable "currentSort" does not exist.', 48, $this->source); })()) == "least_followers")) ? ("selected") : (""));
        yield ">Least Followers</option>
                </select>
            </div>
            <div class=\"col-lg-2 col-md-2 d-grid\">
                <a class=\"btn btn-outline-primary\" href=\"";
        // line 52
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 52, $this->source); })()), "request", [], "any", false, false, false, 52), "pathInfo", [], "any", false, false, false, 52), "html", null, true);
        yield "\">Reset</a>
            </div>

            <div class=\"col-12\">
                <div class=\"accordion\" id=\"filtersAccordion\">
                    <div class=\"accordion-item border-0\">
                        <h2 class=\"accordion-header\">
                            <button class=\"accordion-button collapsed py-2 px-0 bg-transparent shadow-none\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#filtersCollapse\" aria-expanded=\"false\" aria-controls=\"filtersCollapse\">
                                Advanced Filters
                            </button>
                        </h2>
                        <div id=\"filtersCollapse\" class=\"accordion-collapse collapse\">
                            <div class=\"accordion-body px-0 pb-0\">
                                <div class=\"row g-3\">
                                    <div class=\"col-md-4\">
                                        <label class=\"form-label d-block\">Status</label>
                                        <div class=\"form-check\">
                                            <input class=\"form-check-input\" type=\"checkbox\" name=\"status[]\" value=\"open\" id=\"status_open\" ";
        // line 69
        yield ((CoreExtension::inFilter("open", (isset($context["activeStatuses"]) || array_key_exists("activeStatuses", $context) ? $context["activeStatuses"] : (function () { throw new RuntimeError('Variable "activeStatuses" does not exist.', 69, $this->source); })()))) ? ("checked") : (""));
        yield ">
                                            <label class=\"form-check-label\" for=\"status_open\">Open</label>
                                        </div>
                                        <div class=\"form-check\">
                                            <input class=\"form-check-input\" type=\"checkbox\" name=\"status[]\" value=\"locked\" id=\"status_locked\" ";
        // line 73
        yield ((CoreExtension::inFilter("locked", (isset($context["activeStatuses"]) || array_key_exists("activeStatuses", $context) ? $context["activeStatuses"] : (function () { throw new RuntimeError('Variable "activeStatuses" does not exist.', 73, $this->source); })()))) ? ("checked") : (""));
        yield ">
                                            <label class=\"form-check-label\" for=\"status_locked\">Locked</label>
                                        </div>
                                    </div>

                                    <div class=\"col-md-4\">
                                        <label class=\"form-label d-block\">Type</label>
                                        <div class=\"form-check\">
                                            <input class=\"form-check-input\" type=\"checkbox\" name=\"type[]\" value=\"discussion\" id=\"type_discussion\" ";
        // line 81
        yield ((CoreExtension::inFilter("discussion", (isset($context["activeTypes"]) || array_key_exists("activeTypes", $context) ? $context["activeTypes"] : (function () { throw new RuntimeError('Variable "activeTypes" does not exist.', 81, $this->source); })()))) ? ("checked") : (""));
        yield ">
                                            <label class=\"form-check-label\" for=\"type_discussion\">Discussion</label>
                                        </div>
                                        <div class=\"form-check\">
                                            <input class=\"form-check-input\" type=\"checkbox\" name=\"type[]\" value=\"question\" id=\"type_question\" ";
        // line 85
        yield ((CoreExtension::inFilter("question", (isset($context["activeTypes"]) || array_key_exists("activeTypes", $context) ? $context["activeTypes"] : (function () { throw new RuntimeError('Variable "activeTypes" does not exist.', 85, $this->source); })()))) ? ("checked") : (""));
        yield ">
                                            <label class=\"form-check-label\" for=\"type_question\">Question</label>
                                        </div>
                                        <div class=\"form-check\">
                                            <input class=\"form-check-input\" type=\"checkbox\" name=\"type[]\" value=\"announcement\" id=\"type_announcement\" ";
        // line 89
        yield ((CoreExtension::inFilter("announcement", (isset($context["activeTypes"]) || array_key_exists("activeTypes", $context) ? $context["activeTypes"] : (function () { throw new RuntimeError('Variable "activeTypes" does not exist.', 89, $this->source); })()))) ? ("checked") : (""));
        yield ">
                                            <label class=\"form-check-label\" for=\"type_announcement\">Announcement</label>
                                        </div>
                                    </div>

                                    <div class=\"col-md-4\">
                                        <label class=\"form-label d-block\">Categories</label>
                                        <div class=\"category-scroll\">
                                            ";
        // line 97
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["categories"]) || array_key_exists("categories", $context) ? $context["categories"] : (function () { throw new RuntimeError('Variable "categories" does not exist.', 97, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["category"]) {
            // line 98
            yield "                                                <div class=\"form-check\">
                                                    <input class=\"form-check-input\" type=\"checkbox\" name=\"category[]\" value=\"";
            // line 99
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["category"], "id", [], "any", false, false, false, 99), "html", null, true);
            yield "\" id=\"category_";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["category"], "id", [], "any", false, false, false, 99), "html", null, true);
            yield "\" ";
            yield ((CoreExtension::inFilter(CoreExtension::getAttribute($this->env, $this->source, $context["category"], "id", [], "any", false, false, false, 99), (isset($context["activeCategories"]) || array_key_exists("activeCategories", $context) ? $context["activeCategories"] : (function () { throw new RuntimeError('Variable "activeCategories" does not exist.', 99, $this->source); })()))) ? ("checked") : (""));
            yield ">
                                                    <label class=\"form-check-label\" for=\"category_";
            // line 100
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["category"], "id", [], "any", false, false, false, 100), "html", null, true);
            yield "\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["category"], "name", [], "any", false, false, false, 100), "html", null, true);
            yield "</label>
                                                </div>
                                            ";
            $context['_iterated'] = true;
        }
        // line 102
        if (!$context['_iterated']) {
            // line 103
            yield "                                                <p class=\"small text-muted mb-0\">No categories</p>
                                            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['category'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 105
        yield "                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class=\"col-12 d-flex gap-2\">
                <button class=\"btn btn-outline-primary\" type=\"submit\">Apply Filters</button>
            </div>
        </form>

        <div class=\"row g-3\">
            ";
        // line 120
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["threads"]) || array_key_exists("threads", $context) ? $context["threads"] : (function () { throw new RuntimeError('Variable "threads" does not exist.', 120, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["thread"]) {
            // line 121
            yield "                <div class=\"col-12\">
                    <article class=\"card-soft thread-card ";
            // line 122
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["thread"], "isPinned", [], "any", false, false, false, 122)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("thread-card-pinned") : (""));
            yield " p-3 p-md-4\">
                        ";
            // line 123
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["thread"], "isPinned", [], "any", false, false, false, 123)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 124
                yield "                            <div class=\"pinned-flag mb-2\">
                                <i class=\"ni ni-pin-3\"></i>
                                <span>Pinned Spotlight</span>
                            </div>
                        ";
            }
            // line 129
            yield "                        <div class=\"d-flex justify-content-between align-items-start mb-2 gap-2\">
                            <div>
                                <h2 class=\"h5 mb-1\">
                                    <a class=\"text-decoration-none text-dark\" href=\"";
            // line 132
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_forum_thread_detail", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["thread"], "id", [], "any", false, false, false, 132)]), "html", null, true);
            yield "\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["thread"], "title", [], "any", false, false, false, 132), "html", null, true);
            yield "</a>
                                </h2>
                                <p class=\"text-muted mb-2 small\">By ";
            // line 134
            yield ((CoreExtension::getAttribute($this->env, $this->source, $context["thread"], "authorUsername", [], "any", false, false, false, 134)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["thread"], "authorUsername", [], "any", false, false, false, 134), "html", null, true)) : ("Unknown User"));
            yield " in ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["thread"], "category", [], "any", false, false, false, 134), "name", [], "any", false, false, false, 134), "html", null, true);
            yield " • ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["thread"], "createdAt", [], "any", false, false, false, 134), "Y-m-d H:i"), "html", null, true);
            yield "</p>
                            </div>
                            <span class=\"meta-pill ";
            // line 136
            yield (((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["thread"], "status", [], "any", false, false, false, 136), "value", [], "any", false, false, false, 136) == "open")) ? ("meta-open") : ((((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["thread"], "status", [], "any", false, false, false, 136), "value", [], "any", false, false, false, 136) == "locked")) ? ("meta-locked") : ("meta-archived"))));
            yield "\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::upper($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["thread"], "status", [], "any", false, false, false, 136), "value", [], "any", false, false, false, 136)), "html", null, true);
            yield "</span>
                        </div>
                        <p class=\"mb-3\">";
            // line 138
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::slice($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["thread"], "content", [], "any", false, false, false, 138), 0, 240), "html", null, true);
            if ((Twig\Extension\CoreExtension::length($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["thread"], "content", [], "any", false, false, false, 138)) > 240)) {
                yield "...";
            }
            yield "</p>
                        <div class=\"thread-meta-grid d-flex flex-wrap gap-2 small text-muted\">
                            <span class=\"thread-meta-item\">Type: ";
            // line 140
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["thread"], "type", [], "any", false, false, false, 140), "value", [], "any", false, false, false, 140), "html", null, true);
            yield "</span>
                            <span class=\"thread-meta-item\">Likes: ";
            // line 141
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["thread"], "likeCount", [], "any", false, false, false, 141), "html", null, true);
            yield "</span>
                            <span class=\"thread-meta-item\">Dislikes: ";
            // line 142
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["thread"], "dislikeCount", [], "any", false, false, false, 142), "html", null, true);
            yield "</span>
                            <span class=\"thread-meta-item\">Replies: ";
            // line 143
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["thread"], "replyCount", [], "any", false, false, false, 143), "html", null, true);
            yield "</span>
                            <span class=\"thread-meta-item\">Followers: ";
            // line 144
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["thread"], "followCount", [], "any", false, false, false, 144), "html", null, true);
            yield "</span>
                            ";
            // line 145
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["thread"], "isPinned", [], "any", false, false, false, 145)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                yield "<span class=\"badge badge-info\">Pinned</span>";
            }
            // line 146
            yield "                            ";
            if ((CoreExtension::getAttribute($this->env, $this->source, $context["thread"], "authorId", [], "any", false, false, false, 146) == CoreExtension::getAttribute($this->env, $this->source, (isset($context["currentUser"]) || array_key_exists("currentUser", $context) ? $context["currentUser"] : (function () { throw new RuntimeError('Variable "currentUser" does not exist.', 146, $this->source); })()), "id", [], "any", false, false, false, 146))) {
                yield "<span class=\"badge badge-primary\">Your Thread</span>";
            }
            // line 147
            yield "                        </div>
                    </article>
                </div>
            ";
            $context['_iterated'] = true;
        }
        // line 150
        if (!$context['_iterated']) {
            // line 151
            yield "                <p class=\"text-muted\">No threads found.</p>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['thread'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 153
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
        return "forum/feed.html.twig";
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
        return array (  400 => 153,  393 => 151,  391 => 150,  384 => 147,  379 => 146,  375 => 145,  371 => 144,  367 => 143,  363 => 142,  359 => 141,  355 => 140,  347 => 138,  340 => 136,  331 => 134,  324 => 132,  319 => 129,  312 => 124,  310 => 123,  306 => 122,  303 => 121,  298 => 120,  281 => 105,  274 => 103,  272 => 102,  263 => 100,  255 => 99,  252 => 98,  247 => 97,  236 => 89,  229 => 85,  222 => 81,  211 => 73,  204 => 69,  184 => 52,  177 => 48,  173 => 47,  169 => 46,  165 => 45,  161 => 44,  157 => 43,  153 => 42,  149 => 41,  139 => 34,  126 => 24,  116 => 17,  112 => 16,  100 => 6,  87 => 5,  64 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}Forum Feed{% endblock %}

{% block body %}
<main class=\"page-shell\">
    <div class=\"container stats-shell\">
        <section class=\"hero-shell hero-shell-forum mb-4 p-4 p-md-5\">
            <div class=\"d-flex flex-wrap justify-content-between align-items-center gap-3\">
                <div>
                    <p class=\"text-uppercase fw-bold mb-1 small text-primary section-kicker\">Serinity Community</p>
                    <h1 class=\"display-6 fw-bold mb-2\">Forum Posts</h1>
                    <p class=\"m-0 text-muted\">Share questions, support others, and track meaningful conversations.</p>
                </div>
                <div class=\"stats-chip-wrap\">
                    <span class=\"stats-chip\">Threads: {{ threads|length }}</span>
                    <span class=\"stats-chip\">User: {{ currentUser.username }}</span>
                </div>
            </div>
        </section>

        <section class=\"card-soft action-strip p-3 mb-3 d-flex flex-wrap gap-2 justify-content-between align-items-center\">
            <h2 class=\"h4 m-0\">Browse Discussions</h2>
            <a class=\"btn btn-primary btn-icon\" href=\"{{ path('app_thread_new') }}\">
                <span class=\"btn-inner--icon\"><i class=\"ni ni-fat-add\"></i></span>
                <span class=\"btn-inner--text\">New Thread</span>
            </a>
        </section>

        <form method=\"get\" class=\"card-soft filter-strip p-3 mb-4 row g-2 align-items-end\" id=\"feedFilterForm\">
            <div class=\"col-lg-6 col-md-6\">
                <label class=\"form-label\">Search</label>
                <div class=\"d-flex gap-2\">
                    <input type=\"text\" name=\"q\" value=\"{{ app.request.query.get('q') }}\" class=\"form-control\" placeholder=\"Search title...\">
                    <button class=\"btn btn-primary px-3\" type=\"submit\">Search</button>
                </div>
            </div>
            <div class=\"col-lg-4 col-md-4\">
                <label class=\"form-label\">Sort by</label>
                <select name=\"sort\" class=\"form-select\" onchange=\"this.form.requestSubmit()\">
                    <option value=\"newest\" {{ currentSort == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value=\"oldest\" {{ currentSort == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value=\"most_points\" {{ currentSort == 'most_points' ? 'selected' : '' }}>Most Points</option>
                    <option value=\"least_points\" {{ currentSort == 'least_points' ? 'selected' : '' }}>Least Points</option>
                    <option value=\"most_comments\" {{ currentSort == 'most_comments' ? 'selected' : '' }}>Most Comments</option>
                    <option value=\"least_comments\" {{ currentSort == 'least_comments' ? 'selected' : '' }}>Least Comments</option>
                    <option value=\"most_followers\" {{ currentSort == 'most_followers' ? 'selected' : '' }}>Most Followers</option>
                    <option value=\"least_followers\" {{ currentSort == 'least_followers' ? 'selected' : '' }}>Least Followers</option>
                </select>
            </div>
            <div class=\"col-lg-2 col-md-2 d-grid\">
                <a class=\"btn btn-outline-primary\" href=\"{{ app.request.pathInfo }}\">Reset</a>
            </div>

            <div class=\"col-12\">
                <div class=\"accordion\" id=\"filtersAccordion\">
                    <div class=\"accordion-item border-0\">
                        <h2 class=\"accordion-header\">
                            <button class=\"accordion-button collapsed py-2 px-0 bg-transparent shadow-none\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#filtersCollapse\" aria-expanded=\"false\" aria-controls=\"filtersCollapse\">
                                Advanced Filters
                            </button>
                        </h2>
                        <div id=\"filtersCollapse\" class=\"accordion-collapse collapse\">
                            <div class=\"accordion-body px-0 pb-0\">
                                <div class=\"row g-3\">
                                    <div class=\"col-md-4\">
                                        <label class=\"form-label d-block\">Status</label>
                                        <div class=\"form-check\">
                                            <input class=\"form-check-input\" type=\"checkbox\" name=\"status[]\" value=\"open\" id=\"status_open\" {{ 'open' in activeStatuses ? 'checked' : '' }}>
                                            <label class=\"form-check-label\" for=\"status_open\">Open</label>
                                        </div>
                                        <div class=\"form-check\">
                                            <input class=\"form-check-input\" type=\"checkbox\" name=\"status[]\" value=\"locked\" id=\"status_locked\" {{ 'locked' in activeStatuses ? 'checked' : '' }}>
                                            <label class=\"form-check-label\" for=\"status_locked\">Locked</label>
                                        </div>
                                    </div>

                                    <div class=\"col-md-4\">
                                        <label class=\"form-label d-block\">Type</label>
                                        <div class=\"form-check\">
                                            <input class=\"form-check-input\" type=\"checkbox\" name=\"type[]\" value=\"discussion\" id=\"type_discussion\" {{ 'discussion' in activeTypes ? 'checked' : '' }}>
                                            <label class=\"form-check-label\" for=\"type_discussion\">Discussion</label>
                                        </div>
                                        <div class=\"form-check\">
                                            <input class=\"form-check-input\" type=\"checkbox\" name=\"type[]\" value=\"question\" id=\"type_question\" {{ 'question' in activeTypes ? 'checked' : '' }}>
                                            <label class=\"form-check-label\" for=\"type_question\">Question</label>
                                        </div>
                                        <div class=\"form-check\">
                                            <input class=\"form-check-input\" type=\"checkbox\" name=\"type[]\" value=\"announcement\" id=\"type_announcement\" {{ 'announcement' in activeTypes ? 'checked' : '' }}>
                                            <label class=\"form-check-label\" for=\"type_announcement\">Announcement</label>
                                        </div>
                                    </div>

                                    <div class=\"col-md-4\">
                                        <label class=\"form-label d-block\">Categories</label>
                                        <div class=\"category-scroll\">
                                            {% for category in categories %}
                                                <div class=\"form-check\">
                                                    <input class=\"form-check-input\" type=\"checkbox\" name=\"category[]\" value=\"{{ category.id }}\" id=\"category_{{ category.id }}\" {{ category.id in activeCategories ? 'checked' : '' }}>
                                                    <label class=\"form-check-label\" for=\"category_{{ category.id }}\">{{ category.name }}</label>
                                                </div>
                                            {% else %}
                                                <p class=\"small text-muted mb-0\">No categories</p>
                                            {% endfor %}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class=\"col-12 d-flex gap-2\">
                <button class=\"btn btn-outline-primary\" type=\"submit\">Apply Filters</button>
            </div>
        </form>

        <div class=\"row g-3\">
            {% for thread in threads %}
                <div class=\"col-12\">
                    <article class=\"card-soft thread-card {{ thread.isPinned ? 'thread-card-pinned' : '' }} p-3 p-md-4\">
                        {% if thread.isPinned %}
                            <div class=\"pinned-flag mb-2\">
                                <i class=\"ni ni-pin-3\"></i>
                                <span>Pinned Spotlight</span>
                            </div>
                        {% endif %}
                        <div class=\"d-flex justify-content-between align-items-start mb-2 gap-2\">
                            <div>
                                <h2 class=\"h5 mb-1\">
                                    <a class=\"text-decoration-none text-dark\" href=\"{{ path('app_forum_thread_detail', {id: thread.id}) }}\">{{ thread.title }}</a>
                                </h2>
                                <p class=\"text-muted mb-2 small\">By {{ thread.authorUsername ?: 'Unknown User' }} in {{ thread.category.name }} • {{ thread.createdAt|date('Y-m-d H:i') }}</p>
                            </div>
                            <span class=\"meta-pill {{ thread.status.value == 'open' ? 'meta-open' : (thread.status.value == 'locked' ? 'meta-locked' : 'meta-archived') }}\">{{ thread.status.value|upper }}</span>
                        </div>
                        <p class=\"mb-3\">{{ thread.content|slice(0, 240) }}{% if thread.content|length > 240 %}...{% endif %}</p>
                        <div class=\"thread-meta-grid d-flex flex-wrap gap-2 small text-muted\">
                            <span class=\"thread-meta-item\">Type: {{ thread.type.value }}</span>
                            <span class=\"thread-meta-item\">Likes: {{ thread.likeCount }}</span>
                            <span class=\"thread-meta-item\">Dislikes: {{ thread.dislikeCount }}</span>
                            <span class=\"thread-meta-item\">Replies: {{ thread.replyCount }}</span>
                            <span class=\"thread-meta-item\">Followers: {{ thread.followCount }}</span>
                            {% if thread.isPinned %}<span class=\"badge badge-info\">Pinned</span>{% endif %}
                            {% if thread.authorId == currentUser.id %}<span class=\"badge badge-primary\">Your Thread</span>{% endif %}
                        </div>
                    </article>
                </div>
            {% else %}
                <p class=\"text-muted\">No threads found.</p>
            {% endfor %}
        </div>
    </div>
</main>
{% endblock %}
", "forum/feed.html.twig", "C:\\Users\\saifd\\Documents\\serintiy\\serinity-web\\templates\\forum\\feed.html.twig");
    }
}
