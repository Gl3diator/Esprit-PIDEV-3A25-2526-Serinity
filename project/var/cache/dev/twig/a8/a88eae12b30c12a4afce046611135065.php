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

/* forum/thread_detail.html.twig */
class __TwigTemplate_be22ec1dd5b8372b54cd25d7a659f3e5 extends Template
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
            'javascripts' => [$this, 'block_javascripts'],
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "forum/thread_detail.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "forum/thread_detail.html.twig"));

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

        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 3, $this->source); })()), "title", [], "any", false, false, false, 3), "html", null, true);
        
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
        <a href=\"";
        // line 8
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_forum_feed");
        yield "\" class=\"btn btn-sm btn-outline-primary mb-3\">Back to feed</a>

        <section class=\"hero-shell p-4 p-md-5 mb-3\">
            <div class=\"d-flex flex-wrap justify-content-between align-items-start gap-3\">
                <div>
                    <h1 class=\"h3\">";
        // line 13
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 13, $this->source); })()), "title", [], "any", false, false, false, 13), "html", null, true);
        yield "</h1>
                    <p class=\"text-muted\">By ";
        // line 14
        yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 14, $this->source); })()), "authorUsername", [], "any", false, false, false, 14)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 14, $this->source); })()), "authorUsername", [], "any", false, false, false, 14), "html", null, true)) : ("Unknown User"));
        yield " • ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 14, $this->source); })()), "category", [], "any", false, false, false, 14), "name", [], "any", false, false, false, 14), "html", null, true);
        yield " • ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 14, $this->source); })()), "createdAt", [], "any", false, false, false, 14), "Y-m-d H:i"), "html", null, true);
        yield "</p>
                </div>
                <div class=\"d-flex flex-wrap gap-2\">
                    <a class=\"btn btn-sm ";
        // line 17
        yield ((((isset($context["currentVote"]) || array_key_exists("currentVote", $context) ? $context["currentVote"] : (function () { throw new RuntimeError('Variable "currentVote" does not exist.', 17, $this->source); })()) == 1)) ? ("btn-success") : ("btn-outline-success"));
        yield "\" href=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_thread_upvote", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 17, $this->source); })()), "id", [], "any", false, false, false, 17)]), "html", null, true);
        yield "\">Upvote</a>
                    <a class=\"btn btn-sm ";
        // line 18
        yield ((((isset($context["currentVote"]) || array_key_exists("currentVote", $context) ? $context["currentVote"] : (function () { throw new RuntimeError('Variable "currentVote" does not exist.', 18, $this->source); })()) ==  -1)) ? ("btn-danger") : ("btn-outline-danger"));
        yield "\" href=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_thread_downvote", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 18, $this->source); })()), "id", [], "any", false, false, false, 18)]), "html", null, true);
        yield "\">Downvote</a>
                    <a class=\"btn btn-sm ";
        // line 19
        yield (((($tmp = (isset($context["isFollowing"]) || array_key_exists("isFollowing", $context) ? $context["isFollowing"] : (function () { throw new RuntimeError('Variable "isFollowing" does not exist.', 19, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("btn-dark") : ("btn-outline-primary"));
        yield "\" href=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_thread_follow", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 19, $this->source); })()), "id", [], "any", false, false, false, 19)]), "html", null, true);
        yield "\">";
        yield (((($tmp = (isset($context["isFollowing"]) || array_key_exists("isFollowing", $context) ? $context["isFollowing"] : (function () { throw new RuntimeError('Variable "isFollowing" does not exist.', 19, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Unfollow") : ("Follow"));
        yield "</a>
                </div>
            </div>
            <div class=\"card-soft p-3 p-md-4 mt-3 thread-content-block\">
                <p class=\"mb-0\">";
        // line 23
        yield Twig\Extension\CoreExtension::nl2br($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 23, $this->source); })()), "content", [], "any", false, false, false, 23), "html", null, true));
        yield "</p>
            </div>

            ";
        // line 26
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 26, $this->source); })()), "imageUrl", [], "any", false, false, false, 26)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 27
            yield "                <img src=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 27, $this->source); })()), "imageUrl", [], "any", false, false, false, 27), "html", null, true);
            yield "\" alt=\"Thread image\" class=\"img-fluid rounded border mt-3\">
            ";
        }
        // line 29
        yield "        </section>

        <section class=\"card-soft action-strip p-3 mb-3\">
            <div class=\"d-flex flex-wrap gap-2\">
                ";
        // line 33
        if ((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 33, $this->source); })()), "status", [], "any", false, false, false, 33), "value", [], "any", false, false, false, 33) != "locked")) {
            // line 34
            yield "                    <button type=\"button\" class=\"btn btn-sm btn-primary js-open-reply-layer\" id=\"threadAddReplyBtn\">Add Reply</button>
                ";
        }
        // line 36
        yield "                <a class=\"btn btn-sm btn-outline-primary\" href=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_forum_thread_detail", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 36, $this->source); })()), "id", [], "any", false, false, false, 36), "summarize" => 1]), "html", null, true);
        yield "\">Summarize</a>
                <a class=\"btn btn-sm btn-outline-primary\" href=\"";
        // line 37
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_forum_thread_detail", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 37, $this->source); })()), "id", [], "any", false, false, false, 37), "lang" => "French"]), "html", null, true);
        yield "\">Translate FR</a>
                <a class=\"btn btn-sm btn-outline-primary\" href=\"";
        // line 38
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_forum_thread_detail", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 38, $this->source); })()), "id", [], "any", false, false, false, 38), "lang" => "Arabic"]), "html", null, true);
        yield "\">Translate AR</a>
                ";
        // line 39
        if ((($tmp = (isset($context["canManageThread"]) || array_key_exists("canManageThread", $context) ? $context["canManageThread"] : (function () { throw new RuntimeError('Variable "canManageThread" does not exist.', 39, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 40
            yield "                    <a class=\"btn btn-sm btn-outline-primary\" href=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_thread_edit", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 40, $this->source); })()), "id", [], "any", false, false, false, 40)]), "html", null, true);
            yield "\">Edit</a>
                    ";
            // line 41
            if ((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 41, $this->source); })()), "status", [], "any", false, false, false, 41), "value", [], "any", false, false, false, 41) != "archived")) {
                // line 42
                yield "                        <a class=\"btn btn-sm btn-outline-warning\" href=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_thread_status", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 42, $this->source); })()), "id", [], "any", false, false, false, 42), "status" => "archived"]), "html", null, true);
                yield "\">Archive</a>
                    ";
            }
            // line 44
            yield "                    ";
            if ((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 44, $this->source); })()), "status", [], "any", false, false, false, 44), "value", [], "any", false, false, false, 44) == "locked")) {
                // line 45
                yield "                        <a class=\"btn btn-sm btn-outline-success\" href=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_thread_status", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 45, $this->source); })()), "id", [], "any", false, false, false, 45), "status" => "open"]), "html", null, true);
                yield "\">Open</a>
                    ";
            } else {
                // line 47
                yield "                        <a class=\"btn btn-sm btn-outline-secondary\" href=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_thread_status", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 47, $this->source); })()), "id", [], "any", false, false, false, 47), "status" => "locked"]), "html", null, true);
                yield "\">Lock</a>
                    ";
            }
            // line 49
            yield "                    <form method=\"post\" action=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_thread_delete", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 49, $this->source); })()), "id", [], "any", false, false, false, 49)]), "html", null, true);
            yield "\" class=\"d-inline\">
                        <input type=\"hidden\" name=\"_token\" value=\"";
            // line 50
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken(("delete_thread_" . CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 50, $this->source); })()), "id", [], "any", false, false, false, 50))), "html", null, true);
            yield "\">
                        <button type=\"submit\" class=\"btn btn-sm btn-outline-danger\" onclick=\"return confirm('Delete this thread permanently?')\">Delete</button>
                    </form>
                ";
        }
        // line 54
        yield "            </div>
        </section>

        ";
        // line 57
        if ((($tmp = (isset($context["summary"]) || array_key_exists("summary", $context) ? $context["summary"] : (function () { throw new RuntimeError('Variable "summary" does not exist.', 57, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 58
            yield "            <section class=\"card-soft p-3 p-md-4 mb-3\">
                <h2 class=\"h6 text-uppercase section-kicker mb-2\">Summary</h2>
                <p class=\"mb-0\">";
            // line 60
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["summary"]) || array_key_exists("summary", $context) ? $context["summary"] : (function () { throw new RuntimeError('Variable "summary" does not exist.', 60, $this->source); })()), "html", null, true);
            yield "</p>
            </section>
        ";
        }
        // line 63
        yield "
        ";
        // line 64
        if ((($tmp = (isset($context["translated"]) || array_key_exists("translated", $context) ? $context["translated"] : (function () { throw new RuntimeError('Variable "translated" does not exist.', 64, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 65
            yield "            <section class=\"card-soft p-3 p-md-4 mb-3\">
                <h2 class=\"h6 text-uppercase section-kicker mb-2\">Translated Content</h2>
                <p class=\"mb-0\">";
            // line 67
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["translated"]) || array_key_exists("translated", $context) ? $context["translated"] : (function () { throw new RuntimeError('Variable "translated" does not exist.', 67, $this->source); })()), "html", null, true);
            yield "</p>
            </section>
        ";
        }
        // line 70
        yield "
        ";
        // line 71
        if ((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 71, $this->source); })()), "status", [], "any", false, false, false, 71), "value", [], "any", false, false, false, 71) == "locked")) {
            // line 72
            yield "            <section class=\"card-soft p-3 p-md-4 mb-3\">
                <div class=\"alert alert-warning mb-0\">This thread is locked. Replies are disabled.</div>
            </section>
        ";
        } else {
            // line 76
            yield "            <div class=\"reply-layer-backdrop\" id=\"replyLayerBackdrop\"></div>
            <section class=\"reply-layer\" id=\"replyLayer\" aria-hidden=\"true\" role=\"dialog\" aria-label=\"Add reply panel\">
                <div class=\"reply-layer-head p-3 p-md-4 d-flex justify-content-between align-items-start gap-2\">
                    <div>
                        <h2 class=\"h5 m-0\">Add Reply</h2>
                        <p class=\"reply-layer-context m-0\" id=\"replyLayerContext\">Posting a reply to the thread.</p>
                    </div>
                    <button type=\"button\" class=\"btn btn-sm btn-outline-primary\" id=\"replyLayerCloseBtn\">Close</button>
                </div>
                <div class=\"p-3 p-md-4\">
                    ";
            // line 86
            yield             $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderBlock((isset($context["replyForm"]) || array_key_exists("replyForm", $context) ? $context["replyForm"] : (function () { throw new RuntimeError('Variable "replyForm" does not exist.', 86, $this->source); })()), 'form_start', ["attr" => ["novalidate" => "novalidate"]]);
            yield "
                    ";
            // line 87
            yield $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(CoreExtension::getAttribute($this->env, $this->source, (isset($context["replyForm"]) || array_key_exists("replyForm", $context) ? $context["replyForm"] : (function () { throw new RuntimeError('Variable "replyForm" does not exist.', 87, $this->source); })()), "parentId", [], "any", false, false, false, 87), 'widget', ["value" => CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 87, $this->source); })()), "request", [], "any", false, false, false, 87), "query", [], "any", false, false, false, 87), "get", ["reply_to"], "method", false, false, false, 87)]);
            yield "
                    ";
            // line 88
            yield $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(CoreExtension::getAttribute($this->env, $this->source, (isset($context["replyForm"]) || array_key_exists("replyForm", $context) ? $context["replyForm"] : (function () { throw new RuntimeError('Variable "replyForm" does not exist.', 88, $this->source); })()), "content", [], "any", false, false, false, 88), 'row');
            yield "
                    <div class=\"d-flex gap-2\">
                        <button class=\"btn btn-primary\">Post Reply</button>
                        <button type=\"button\" class=\"btn btn-outline-primary\" id=\"replyLayerCancelBtn\">Cancel</button>
                    </div>
                    ";
            // line 93
            yield             $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderBlock((isset($context["replyForm"]) || array_key_exists("replyForm", $context) ? $context["replyForm"] : (function () { throw new RuntimeError('Variable "replyForm" does not exist.', 93, $this->source); })()), 'form_end');
            yield "
                </div>
            </section>
        ";
        }
        // line 97
        yield "
        <section class=\"card-soft p-3 p-md-4\">
            <h2 class=\"h5 mb-3\">Replies</h2>
            ";
        // line 100
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["replies"]) || array_key_exists("replies", $context) ? $context["replies"] : (function () { throw new RuntimeError('Variable "replies" does not exist.', 100, $this->source); })()));
        $context['_iterated'] = false;
        $context['loop'] = [
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        ];
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["reply"]) {
            // line 101
            yield "                ";
            yield Twig\Extension\CoreExtension::include($this->env, $context, "forum/_reply_item.html.twig", ["reply" => $context["reply"], "thread" => (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 101, $this->source); })()), "currentUser" => (isset($context["currentUser"]) || array_key_exists("currentUser", $context) ? $context["currentUser"] : (function () { throw new RuntimeError('Variable "currentUser" does not exist.', 101, $this->source); })()), "level" => 0]);
            yield "
            ";
            $context['_iterated'] = true;
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['revindex0'], $context['loop']['revindex'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        // line 102
        if (!$context['_iterated']) {
            // line 103
            yield "                <p class=\"text-muted\">No replies yet.</p>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['reply'], $context['_parent'], $context['_iterated'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 105
        yield "        </section>
    </div>
</main>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 110
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

        // line 111
        yield "    ";
        yield from $this->yieldParentBlock("javascripts", $context, $blocks);
        yield "
    <script>
        (function () {
            const initReplyLayer = () => {
                const layer = document.getElementById('replyLayer');
                const backdrop = document.getElementById('replyLayerBackdrop');

                if (!layer || !backdrop || layer.dataset.bound === '1') {
                    return;
                }

                layer.dataset.bound = '1';

                const closeBtn = document.getElementById('replyLayerCloseBtn');
                const cancelBtn = document.getElementById('replyLayerCancelBtn');
                const contextEl = document.getElementById('replyLayerContext');
                const parentInput = layer.querySelector('input[name\$=\"[parentId]\"]');

                const setReplyContext = (replyId) => {
                    if (!parentInput || !contextEl) {
                        return;
                    }

                    if (replyId) {
                        parentInput.value = replyId;
                        contextEl.textContent = `Replying to comment #\${replyId}.`;
                        return;
                    }

                    parentInput.value = '';
                    contextEl.textContent = 'Posting a reply to the thread.';
                };

                const openLayer = (replyId = null) => {
                    setReplyContext(replyId);
                    layer.classList.add('is-open');
                    backdrop.classList.add('is-open');
                    layer.setAttribute('aria-hidden', 'false');
                };

                const closeLayer = () => {
                    layer.classList.remove('is-open');
                    backdrop.classList.remove('is-open');
                    layer.setAttribute('aria-hidden', 'true');
                };

                document.querySelectorAll('.js-open-reply-layer').forEach((trigger) => {
                    trigger.addEventListener('click', () => {
                        const replyId = trigger.dataset.replyId || null;
                        openLayer(replyId);
                    });
                });

                [closeBtn, cancelBtn, backdrop].forEach((el) => {
                    if (el) {
                        el.addEventListener('click', closeLayer);
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && layer.classList.contains('is-open')) {
                        closeLayer();
                    }
                });

                const urlReplyTo = new URLSearchParams(window.location.search).get('reply_to');
                if (urlReplyTo) {
                    openLayer(urlReplyTo);
                }
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initReplyLayer, { once: true });
            } else {
                initReplyLayer();
            }

            document.addEventListener('turbo:load', initReplyLayer);
        })();
    </script>
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
        return "forum/thread_detail.html.twig";
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
        return array (  383 => 111,  370 => 110,  356 => 105,  349 => 103,  347 => 102,  332 => 101,  314 => 100,  309 => 97,  302 => 93,  294 => 88,  290 => 87,  286 => 86,  274 => 76,  268 => 72,  266 => 71,  263 => 70,  257 => 67,  253 => 65,  251 => 64,  248 => 63,  242 => 60,  238 => 58,  236 => 57,  231 => 54,  224 => 50,  219 => 49,  213 => 47,  207 => 45,  204 => 44,  198 => 42,  196 => 41,  191 => 40,  189 => 39,  185 => 38,  181 => 37,  176 => 36,  172 => 34,  170 => 33,  164 => 29,  158 => 27,  156 => 26,  150 => 23,  139 => 19,  133 => 18,  127 => 17,  117 => 14,  113 => 13,  105 => 8,  101 => 6,  88 => 5,  65 => 3,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}{{ thread.title }}{% endblock %}

{% block body %}
<main class=\"page-shell\">
    <div class=\"container stats-shell\">
        <a href=\"{{ path('app_forum_feed') }}\" class=\"btn btn-sm btn-outline-primary mb-3\">Back to feed</a>

        <section class=\"hero-shell p-4 p-md-5 mb-3\">
            <div class=\"d-flex flex-wrap justify-content-between align-items-start gap-3\">
                <div>
                    <h1 class=\"h3\">{{ thread.title }}</h1>
                    <p class=\"text-muted\">By {{ thread.authorUsername ?: 'Unknown User' }} • {{ thread.category.name }} • {{ thread.createdAt|date('Y-m-d H:i') }}</p>
                </div>
                <div class=\"d-flex flex-wrap gap-2\">
                    <a class=\"btn btn-sm {{ currentVote == 1 ? 'btn-success' : 'btn-outline-success' }}\" href=\"{{ path('app_thread_upvote', {id: thread.id}) }}\">Upvote</a>
                    <a class=\"btn btn-sm {{ currentVote == -1 ? 'btn-danger' : 'btn-outline-danger' }}\" href=\"{{ path('app_thread_downvote', {id: thread.id}) }}\">Downvote</a>
                    <a class=\"btn btn-sm {{ isFollowing ? 'btn-dark' : 'btn-outline-primary' }}\" href=\"{{ path('app_thread_follow', {id: thread.id}) }}\">{{ isFollowing ? 'Unfollow' : 'Follow' }}</a>
                </div>
            </div>
            <div class=\"card-soft p-3 p-md-4 mt-3 thread-content-block\">
                <p class=\"mb-0\">{{ thread.content|nl2br }}</p>
            </div>

            {% if thread.imageUrl %}
                <img src=\"{{ thread.imageUrl }}\" alt=\"Thread image\" class=\"img-fluid rounded border mt-3\">
            {% endif %}
        </section>

        <section class=\"card-soft action-strip p-3 mb-3\">
            <div class=\"d-flex flex-wrap gap-2\">
                {% if thread.status.value != 'locked' %}
                    <button type=\"button\" class=\"btn btn-sm btn-primary js-open-reply-layer\" id=\"threadAddReplyBtn\">Add Reply</button>
                {% endif %}
                <a class=\"btn btn-sm btn-outline-primary\" href=\"{{ path('app_forum_thread_detail', {id: thread.id, summarize: 1}) }}\">Summarize</a>
                <a class=\"btn btn-sm btn-outline-primary\" href=\"{{ path('app_forum_thread_detail', {id: thread.id, lang: 'French'}) }}\">Translate FR</a>
                <a class=\"btn btn-sm btn-outline-primary\" href=\"{{ path('app_forum_thread_detail', {id: thread.id, lang: 'Arabic'}) }}\">Translate AR</a>
                {% if canManageThread %}
                    <a class=\"btn btn-sm btn-outline-primary\" href=\"{{ path('app_thread_edit', {id: thread.id}) }}\">Edit</a>
                    {% if thread.status.value != 'archived' %}
                        <a class=\"btn btn-sm btn-outline-warning\" href=\"{{ path('app_thread_status', {id: thread.id, status: 'archived'}) }}\">Archive</a>
                    {% endif %}
                    {% if thread.status.value == 'locked' %}
                        <a class=\"btn btn-sm btn-outline-success\" href=\"{{ path('app_thread_status', {id: thread.id, status: 'open'}) }}\">Open</a>
                    {% else %}
                        <a class=\"btn btn-sm btn-outline-secondary\" href=\"{{ path('app_thread_status', {id: thread.id, status: 'locked'}) }}\">Lock</a>
                    {% endif %}
                    <form method=\"post\" action=\"{{ path('app_thread_delete', {id: thread.id}) }}\" class=\"d-inline\">
                        <input type=\"hidden\" name=\"_token\" value=\"{{ csrf_token('delete_thread_' ~ thread.id) }}\">
                        <button type=\"submit\" class=\"btn btn-sm btn-outline-danger\" onclick=\"return confirm('Delete this thread permanently?')\">Delete</button>
                    </form>
                {% endif %}
            </div>
        </section>

        {% if summary %}
            <section class=\"card-soft p-3 p-md-4 mb-3\">
                <h2 class=\"h6 text-uppercase section-kicker mb-2\">Summary</h2>
                <p class=\"mb-0\">{{ summary }}</p>
            </section>
        {% endif %}

        {% if translated %}
            <section class=\"card-soft p-3 p-md-4 mb-3\">
                <h2 class=\"h6 text-uppercase section-kicker mb-2\">Translated Content</h2>
                <p class=\"mb-0\">{{ translated }}</p>
            </section>
        {% endif %}

        {% if thread.status.value == 'locked' %}
            <section class=\"card-soft p-3 p-md-4 mb-3\">
                <div class=\"alert alert-warning mb-0\">This thread is locked. Replies are disabled.</div>
            </section>
        {% else %}
            <div class=\"reply-layer-backdrop\" id=\"replyLayerBackdrop\"></div>
            <section class=\"reply-layer\" id=\"replyLayer\" aria-hidden=\"true\" role=\"dialog\" aria-label=\"Add reply panel\">
                <div class=\"reply-layer-head p-3 p-md-4 d-flex justify-content-between align-items-start gap-2\">
                    <div>
                        <h2 class=\"h5 m-0\">Add Reply</h2>
                        <p class=\"reply-layer-context m-0\" id=\"replyLayerContext\">Posting a reply to the thread.</p>
                    </div>
                    <button type=\"button\" class=\"btn btn-sm btn-outline-primary\" id=\"replyLayerCloseBtn\">Close</button>
                </div>
                <div class=\"p-3 p-md-4\">
                    {{ form_start(replyForm, {'attr': {'novalidate': 'novalidate'}}) }}
                    {{ form_widget(replyForm.parentId, { value: app.request.query.get('reply_to') }) }}
                    {{ form_row(replyForm.content) }}
                    <div class=\"d-flex gap-2\">
                        <button class=\"btn btn-primary\">Post Reply</button>
                        <button type=\"button\" class=\"btn btn-outline-primary\" id=\"replyLayerCancelBtn\">Cancel</button>
                    </div>
                    {{ form_end(replyForm) }}
                </div>
            </section>
        {% endif %}

        <section class=\"card-soft p-3 p-md-4\">
            <h2 class=\"h5 mb-3\">Replies</h2>
            {% for reply in replies %}
                {{ include('forum/_reply_item.html.twig', { reply: reply, thread: thread, currentUser: currentUser, level: 0 }) }}
            {% else %}
                <p class=\"text-muted\">No replies yet.</p>
            {% endfor %}
        </section>
    </div>
</main>
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    <script>
        (function () {
            const initReplyLayer = () => {
                const layer = document.getElementById('replyLayer');
                const backdrop = document.getElementById('replyLayerBackdrop');

                if (!layer || !backdrop || layer.dataset.bound === '1') {
                    return;
                }

                layer.dataset.bound = '1';

                const closeBtn = document.getElementById('replyLayerCloseBtn');
                const cancelBtn = document.getElementById('replyLayerCancelBtn');
                const contextEl = document.getElementById('replyLayerContext');
                const parentInput = layer.querySelector('input[name\$=\"[parentId]\"]');

                const setReplyContext = (replyId) => {
                    if (!parentInput || !contextEl) {
                        return;
                    }

                    if (replyId) {
                        parentInput.value = replyId;
                        contextEl.textContent = `Replying to comment #\${replyId}.`;
                        return;
                    }

                    parentInput.value = '';
                    contextEl.textContent = 'Posting a reply to the thread.';
                };

                const openLayer = (replyId = null) => {
                    setReplyContext(replyId);
                    layer.classList.add('is-open');
                    backdrop.classList.add('is-open');
                    layer.setAttribute('aria-hidden', 'false');
                };

                const closeLayer = () => {
                    layer.classList.remove('is-open');
                    backdrop.classList.remove('is-open');
                    layer.setAttribute('aria-hidden', 'true');
                };

                document.querySelectorAll('.js-open-reply-layer').forEach((trigger) => {
                    trigger.addEventListener('click', () => {
                        const replyId = trigger.dataset.replyId || null;
                        openLayer(replyId);
                    });
                });

                [closeBtn, cancelBtn, backdrop].forEach((el) => {
                    if (el) {
                        el.addEventListener('click', closeLayer);
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && layer.classList.contains('is-open')) {
                        closeLayer();
                    }
                });

                const urlReplyTo = new URLSearchParams(window.location.search).get('reply_to');
                if (urlReplyTo) {
                    openLayer(urlReplyTo);
                }
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initReplyLayer, { once: true });
            } else {
                initReplyLayer();
            }

            document.addEventListener('turbo:load', initReplyLayer);
        })();
    </script>
{% endblock %}
", "forum/thread_detail.html.twig", "C:\\Users\\saifd\\Documents\\serintiy\\serinity-web\\templates\\forum\\thread_detail.html.twig");
    }
}
