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

/* notification/_floating_widget.html.twig */
class __TwigTemplate_ca3391f58575a476e7b322e4f3d9d5e0 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "notification/_floating_widget.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "notification/_floating_widget.html.twig"));

        // line 1
        yield "<div class=\"floating-notify\" id=\"floatingNotify\">
    <button
        class=\"floating-notify-btn\"
        id=\"floatingNotifyBtn\"
        type=\"button\"
        aria-label=\"Open notifications\"
        aria-expanded=\"false\"
        aria-controls=\"floatingNotifyPanel\"
    >
        <i class=\"ni ni-bell-55\" aria-hidden=\"true\"></i>
        ";
        // line 11
        if (((isset($context["unreadCount"]) || array_key_exists("unreadCount", $context) ? $context["unreadCount"] : (function () { throw new RuntimeError('Variable "unreadCount" does not exist.', 11, $this->source); })()) > 0)) {
            // line 12
            yield "            <span class=\"floating-notify-badge\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["unreadCount"]) || array_key_exists("unreadCount", $context) ? $context["unreadCount"] : (function () { throw new RuntimeError('Variable "unreadCount" does not exist.', 12, $this->source); })()), "html", null, true);
            yield "</span>
        ";
        }
        // line 14
        yield "    </button>

    <div class=\"floating-notify-panel\" id=\"floatingNotifyPanel\" role=\"dialog\" aria-label=\"Notifications panel\">
        <div class=\"floating-notify-head\">
            <h2>Notifications</h2>
            <a href=\"";
        // line 19
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_notification_seen_all");
        yield "\" class=\"floating-notify-clear\">Clear all</a>
        </div>

        <div class=\"floating-notify-list\">
            ";
        // line 23
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["notifications"]) || array_key_exists("notifications", $context) ? $context["notifications"] : (function () { throw new RuntimeError('Variable "notifications" does not exist.', 23, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["notification"]) {
            // line 24
            yield "                <a class=\"floating-notify-item ";
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["notification"], "seen", [], "any", false, false, false, 24)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("") : ("is-unread"));
            yield "\" href=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_notification_open", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["notification"], "id", [], "any", false, false, false, 24)]), "html", null, true);
            yield "\">
                    <p>";
            // line 25
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["notification"], "content", [], "any", false, false, false, 25), "html", null, true);
            yield "</p>
                    <span>";
            // line 26
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["notification"], "createdAt", [], "any", false, false, false, 26), "Y-m-d H:i"), "html", null, true);
            yield "</span>
                </a>
            ";
            $context['_iterated'] = true;
        }
        // line 28
        if (!$context['_iterated']) {
            // line 29
            yield "                <div class=\"floating-notify-empty\">No notifications.</div>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['notification'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 31
        yield "        </div>
    </div>
</div>
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
        return "notification/_floating_widget.html.twig";
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
        return array (  114 => 31,  107 => 29,  105 => 28,  98 => 26,  94 => 25,  87 => 24,  82 => 23,  75 => 19,  68 => 14,  62 => 12,  60 => 11,  48 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<div class=\"floating-notify\" id=\"floatingNotify\">
    <button
        class=\"floating-notify-btn\"
        id=\"floatingNotifyBtn\"
        type=\"button\"
        aria-label=\"Open notifications\"
        aria-expanded=\"false\"
        aria-controls=\"floatingNotifyPanel\"
    >
        <i class=\"ni ni-bell-55\" aria-hidden=\"true\"></i>
        {% if unreadCount > 0 %}
            <span class=\"floating-notify-badge\">{{ unreadCount }}</span>
        {% endif %}
    </button>

    <div class=\"floating-notify-panel\" id=\"floatingNotifyPanel\" role=\"dialog\" aria-label=\"Notifications panel\">
        <div class=\"floating-notify-head\">
            <h2>Notifications</h2>
            <a href=\"{{ path('app_notification_seen_all') }}\" class=\"floating-notify-clear\">Clear all</a>
        </div>

        <div class=\"floating-notify-list\">
            {% for notification in notifications %}
                <a class=\"floating-notify-item {{ notification.seen ? '' : 'is-unread' }}\" href=\"{{ path('app_notification_open', {id: notification.id}) }}\">
                    <p>{{ notification.content }}</p>
                    <span>{{ notification.createdAt|date('Y-m-d H:i') }}</span>
                </a>
            {% else %}
                <div class=\"floating-notify-empty\">No notifications.</div>
            {% endfor %}
        </div>
    </div>
</div>
", "notification/_floating_widget.html.twig", "C:\\Users\\saifd\\Documents\\serintiy\\serinity-web\\templates\\notification\\_floating_widget.html.twig");
    }
}
