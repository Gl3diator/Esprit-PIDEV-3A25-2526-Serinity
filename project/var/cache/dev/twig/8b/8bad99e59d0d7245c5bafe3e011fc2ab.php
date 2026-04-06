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

/* forum/_reply_item.html.twig */
class __TwigTemplate_c172e7c4b65c4383369056b7d536079d extends Template
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
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "forum/_reply_item.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "forum/_reply_item.html.twig"));

        // line 1
        yield "<article class=\"card-soft reply-box p-3 mb-2 ms-";
        yield ((((isset($context["level"]) || array_key_exists("level", $context) ? $context["level"] : (function () { throw new RuntimeError('Variable "level" does not exist.', 1, $this->source); })()) > 0)) ? (4) : (0));
        yield "\">
    <p class=\"small text-muted mb-1\">";
        // line 2
        yield ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["reply"]) || array_key_exists("reply", $context) ? $context["reply"] : (function () { throw new RuntimeError('Variable "reply" does not exist.', 2, $this->source); })()), "authorUsername", [], "any", false, false, false, 2)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["reply"]) || array_key_exists("reply", $context) ? $context["reply"] : (function () { throw new RuntimeError('Variable "reply" does not exist.', 2, $this->source); })()), "authorUsername", [], "any", false, false, false, 2), "html", null, true)) : ("Unknown User"));
        yield " • ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, (isset($context["reply"]) || array_key_exists("reply", $context) ? $context["reply"] : (function () { throw new RuntimeError('Variable "reply" does not exist.', 2, $this->source); })()), "createdAt", [], "any", false, false, false, 2), "Y-m-d H:i"), "html", null, true);
        yield "</p>
    <p class=\"mb-2\">";
        // line 3
        yield Twig\Extension\CoreExtension::nl2br($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["reply"]) || array_key_exists("reply", $context) ? $context["reply"] : (function () { throw new RuntimeError('Variable "reply" does not exist.', 3, $this->source); })()), "content", [], "any", false, false, false, 3), "html", null, true));
        yield "</p>

    <div class=\"d-flex flex-wrap gap-2 mb-2\">
        ";
        // line 6
        if ((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 6, $this->source); })()), "status", [], "any", false, false, false, 6), "value", [], "any", false, false, false, 6) != "locked")) {
            // line 7
            yield "            <button type=\"button\" class=\"btn btn-sm btn-outline-primary js-open-reply-layer\" data-reply-id=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["reply"]) || array_key_exists("reply", $context) ? $context["reply"] : (function () { throw new RuntimeError('Variable "reply" does not exist.', 7, $this->source); })()), "id", [], "any", false, false, false, 7), "html", null, true);
            yield "\">Reply</button>
        ";
        }
        // line 9
        yield "
        ";
        // line 10
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["currentUser"]) || array_key_exists("currentUser", $context) ? $context["currentUser"] : (function () { throw new RuntimeError('Variable "currentUser" does not exist.', 10, $this->source); })()), "id", [], "any", false, false, false, 10) == CoreExtension::getAttribute($this->env, $this->source, (isset($context["reply"]) || array_key_exists("reply", $context) ? $context["reply"] : (function () { throw new RuntimeError('Variable "reply" does not exist.', 10, $this->source); })()), "authorId", [], "any", false, false, false, 10))) {
            // line 11
            yield "            <button class=\"btn btn-sm btn-outline-dark\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#reply-edit-";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["reply"]) || array_key_exists("reply", $context) ? $context["reply"] : (function () { throw new RuntimeError('Variable "reply" does not exist.', 11, $this->source); })()), "id", [], "any", false, false, false, 11), "html", null, true);
            yield "\" aria-expanded=\"false\" aria-controls=\"reply-edit-";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["reply"]) || array_key_exists("reply", $context) ? $context["reply"] : (function () { throw new RuntimeError('Variable "reply" does not exist.', 11, $this->source); })()), "id", [], "any", false, false, false, 11), "html", null, true);
            yield "\">
                Edit
            </button>
            <form method=\"post\" action=\"";
            // line 14
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_reply_delete", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["reply"]) || array_key_exists("reply", $context) ? $context["reply"] : (function () { throw new RuntimeError('Variable "reply" does not exist.', 14, $this->source); })()), "id", [], "any", false, false, false, 14)]), "html", null, true);
            yield "\" class=\"d-inline\">
                <input type=\"hidden\" name=\"_token\" value=\"";
            // line 15
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken(("delete_reply_" . CoreExtension::getAttribute($this->env, $this->source, (isset($context["reply"]) || array_key_exists("reply", $context) ? $context["reply"] : (function () { throw new RuntimeError('Variable "reply" does not exist.', 15, $this->source); })()), "id", [], "any", false, false, false, 15))), "html", null, true);
            yield "\">
                <button type=\"submit\" class=\"btn btn-sm btn-outline-danger\" onclick=\"return confirm('Delete this reply?')\">Delete</button>
            </form>
        ";
        }
        // line 19
        yield "    </div>

    ";
        // line 21
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["currentUser"]) || array_key_exists("currentUser", $context) ? $context["currentUser"] : (function () { throw new RuntimeError('Variable "currentUser" does not exist.', 21, $this->source); })()), "id", [], "any", false, false, false, 21) == CoreExtension::getAttribute($this->env, $this->source, (isset($context["reply"]) || array_key_exists("reply", $context) ? $context["reply"] : (function () { throw new RuntimeError('Variable "reply" does not exist.', 21, $this->source); })()), "authorId", [], "any", false, false, false, 21))) {
            // line 22
            yield "        <div class=\"collapse mb-2\" id=\"reply-edit-";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["reply"]) || array_key_exists("reply", $context) ? $context["reply"] : (function () { throw new RuntimeError('Variable "reply" does not exist.', 22, $this->source); })()), "id", [], "any", false, false, false, 22), "html", null, true);
            yield "\">
            <form method=\"post\" action=\"";
            // line 23
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_reply_edit", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["reply"]) || array_key_exists("reply", $context) ? $context["reply"] : (function () { throw new RuntimeError('Variable "reply" does not exist.', 23, $this->source); })()), "id", [], "any", false, false, false, 23)]), "html", null, true);
            yield "\" class=\"d-flex flex-wrap gap-2 align-items-start\">
                <input type=\"hidden\" name=\"_token\" value=\"";
            // line 24
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken(("edit_reply_" . CoreExtension::getAttribute($this->env, $this->source, (isset($context["reply"]) || array_key_exists("reply", $context) ? $context["reply"] : (function () { throw new RuntimeError('Variable "reply" does not exist.', 24, $this->source); })()), "id", [], "any", false, false, false, 24))), "html", null, true);
            yield "\">
                <textarea name=\"content\" class=\"form-control form-control-sm\" rows=\"2\" style=\"max-width: 520px;\">";
            // line 25
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["reply"]) || array_key_exists("reply", $context) ? $context["reply"] : (function () { throw new RuntimeError('Variable "reply" does not exist.', 25, $this->source); })()), "content", [], "any", false, false, false, 25), "html", null, true);
            yield "</textarea>
                <button type=\"submit\" class=\"btn btn-sm btn-dark\">Save</button>
            </form>
        </div>
    ";
        }
        // line 30
        yield "
    ";
        // line 31
        if ((Twig\Extension\CoreExtension::length($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, (isset($context["reply"]) || array_key_exists("reply", $context) ? $context["reply"] : (function () { throw new RuntimeError('Variable "reply" does not exist.', 31, $this->source); })()), "children", [], "any", false, false, false, 31)) > 0)) {
            // line 32
            yield "        <div class=\"mt-2\">
            ";
            // line 33
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["reply"]) || array_key_exists("reply", $context) ? $context["reply"] : (function () { throw new RuntimeError('Variable "reply" does not exist.', 33, $this->source); })()), "children", [], "any", false, false, false, 33));
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
            foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
                // line 34
                yield "                ";
                yield Twig\Extension\CoreExtension::include($this->env, $context, "forum/_reply_item.html.twig", ["reply" => $context["child"], "thread" => (isset($context["thread"]) || array_key_exists("thread", $context) ? $context["thread"] : (function () { throw new RuntimeError('Variable "thread" does not exist.', 34, $this->source); })()), "currentUser" => (isset($context["currentUser"]) || array_key_exists("currentUser", $context) ? $context["currentUser"] : (function () { throw new RuntimeError('Variable "currentUser" does not exist.', 34, $this->source); })()), "level" => ((isset($context["level"]) || array_key_exists("level", $context) ? $context["level"] : (function () { throw new RuntimeError('Variable "level" does not exist.', 34, $this->source); })()) + 1)]);
                yield "
            ";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['revindex0'], $context['loop']['revindex'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 36
            yield "        </div>
    ";
        }
        // line 38
        yield "</article>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "forum/_reply_item.html.twig";
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
        return array (  171 => 38,  167 => 36,  150 => 34,  133 => 33,  130 => 32,  128 => 31,  125 => 30,  117 => 25,  113 => 24,  109 => 23,  104 => 22,  102 => 21,  98 => 19,  91 => 15,  87 => 14,  78 => 11,  76 => 10,  73 => 9,  67 => 7,  65 => 6,  59 => 3,  53 => 2,  48 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<article class=\"card-soft reply-box p-3 mb-2 ms-{{ level > 0 ? 4 : 0 }}\">
    <p class=\"small text-muted mb-1\">{{ reply.authorUsername ?: 'Unknown User' }} • {{ reply.createdAt|date('Y-m-d H:i') }}</p>
    <p class=\"mb-2\">{{ reply.content|nl2br }}</p>

    <div class=\"d-flex flex-wrap gap-2 mb-2\">
        {% if thread.status.value != 'locked' %}
            <button type=\"button\" class=\"btn btn-sm btn-outline-primary js-open-reply-layer\" data-reply-id=\"{{ reply.id }}\">Reply</button>
        {% endif %}

        {% if currentUser.id == reply.authorId %}
            <button class=\"btn btn-sm btn-outline-dark\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#reply-edit-{{ reply.id }}\" aria-expanded=\"false\" aria-controls=\"reply-edit-{{ reply.id }}\">
                Edit
            </button>
            <form method=\"post\" action=\"{{ path('app_reply_delete', {id: reply.id}) }}\" class=\"d-inline\">
                <input type=\"hidden\" name=\"_token\" value=\"{{ csrf_token('delete_reply_' ~ reply.id) }}\">
                <button type=\"submit\" class=\"btn btn-sm btn-outline-danger\" onclick=\"return confirm('Delete this reply?')\">Delete</button>
            </form>
        {% endif %}
    </div>

    {% if currentUser.id == reply.authorId %}
        <div class=\"collapse mb-2\" id=\"reply-edit-{{ reply.id }}\">
            <form method=\"post\" action=\"{{ path('app_reply_edit', {id: reply.id}) }}\" class=\"d-flex flex-wrap gap-2 align-items-start\">
                <input type=\"hidden\" name=\"_token\" value=\"{{ csrf_token('edit_reply_' ~ reply.id) }}\">
                <textarea name=\"content\" class=\"form-control form-control-sm\" rows=\"2\" style=\"max-width: 520px;\">{{ reply.content }}</textarea>
                <button type=\"submit\" class=\"btn btn-sm btn-dark\">Save</button>
            </form>
        </div>
    {% endif %}

    {% if reply.children|length > 0 %}
        <div class=\"mt-2\">
            {% for child in reply.children %}
                {{ include('forum/_reply_item.html.twig', { reply: child, thread: thread, currentUser: currentUser, level: level + 1 }) }}
            {% endfor %}
        </div>
    {% endif %}
</article>
", "forum/_reply_item.html.twig", "C:\\Users\\saifd\\Documents\\serintiy\\serinity-web\\templates\\forum\\_reply_item.html.twig");
    }
}
