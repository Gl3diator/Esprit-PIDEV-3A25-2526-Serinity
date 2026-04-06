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

/* admin/statistics.html.twig */
class __TwigTemplate_765083a4e490b30a3b2fd305ddd39aab extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "admin/statistics.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "admin/statistics.html.twig"));

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

        yield "Forum Statistics";
        
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
    <div class=\"container stats-shell stats-fxml-theme\">
        <section class=\"card-soft p-3 p-md-4 mb-3 stats-head stats-panel\">
            <div class=\"d-flex flex-wrap justify-content-between align-items-center gap-2\">
                <div class=\"d-flex align-items-center gap-2 gap-md-3\">
                    <a class=\"btn btn-stats-back\" href=\"";
        // line 11
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_admin_forum");
        yield "\">Back</a>
                    <div>
                        <p class=\"mb-1 section-kicker text-uppercase\">Analytics Dashboard</p>
                        <h1 class=\"h3 m-0 stats-title\">Forum Statistics</h1>
                        <p class=\"text-muted small mb-0\">Live overview of forum health, activity, and engagement</p>
                    </div>
                </div>
                <a class=\"btn btn-stats-refresh\" href=\"";
        // line 18
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_admin_statistics");
        yield "\">Refresh</a>
            </div>
        </section>

        <div class=\"row g-3 mb-3\">
            <div class=\"col-6 col-lg-3\">
                <article class=\"card-soft p-3 h-100 stat-card stat-card-blue stats-panel\">
                    <div class=\"stat-icon mb-2\"><i class=\"ni ni-collection\"></i></div>
                    <div class=\"stat-label\">Total Threads</div>
                    <div class=\"stat-value\">";
        // line 27
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 27, $this->source); })()), "totalThreads", [], "any", false, false, false, 27), "html", null, true);
        yield "</div>
                </article>
            </div>
            <div class=\"col-6 col-lg-3\">
                <article class=\"card-soft p-3 h-100 stat-card stat-card-green stats-panel\">
                    <div class=\"stat-icon mb-2\"><i class=\"ni ni-chat-round\"></i></div>
                    <div class=\"stat-label\">Total Replies</div>
                    <div class=\"stat-value\">";
        // line 34
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 34, $this->source); })()), "totalReplies", [], "any", false, false, false, 34), "html", null, true);
        yield "</div>
                </article>
            </div>
            <div class=\"col-6 col-lg-3\">
                <article class=\"card-soft p-3 h-100 stat-card stat-card-orange stats-panel\">
                    <div class=\"stat-icon mb-2\"><i class=\"ni ni-single-02\"></i></div>
                    <div class=\"stat-label\">Total Users</div>
                    <div class=\"stat-value\">";
        // line 41
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 41, $this->source); })()), "totalUsers", [], "any", false, false, false, 41), "html", null, true);
        yield "</div>
                </article>
            </div>
            <div class=\"col-6 col-lg-3\">
                <article class=\"card-soft p-3 h-100 stat-card stat-card-violet stats-panel\">
                    <div class=\"stat-icon mb-2\"><i class=\"ni ni-bullet-list-67\"></i></div>
                    <div class=\"stat-label\">Total Categories</div>
                    <div class=\"stat-value\">";
        // line 48
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 48, $this->source); })()), "totalCategories", [], "any", false, false, false, 48), "html", null, true);
        yield "</div>
                </article>
            </div>
        </div>

        <div class=\"row g-3 mb-3\">
            <div class=\"col-lg-6\">
                <article class=\"card-soft p-3 p-md-4 h-100 chart-panel stats-panel\">
                    <h2 class=\"h6 mb-3\">Thread Status Distribution</h2>
                    <div class=\"chart-wrap chart-wrap-pie\">
                        <canvas id=\"statusPieChart\"></canvas>
                    </div>
                </article>
            </div>
            <div class=\"col-lg-6\">
                <article class=\"card-soft p-3 p-md-4 h-100 chart-panel stats-panel\">
                    <h2 class=\"h6 mb-3\">Thread Type Distribution</h2>
                    <div class=\"chart-wrap chart-wrap-pie\">
                        <canvas id=\"typePieChart\"></canvas>
                    </div>
                </article>
            </div>
        </div>

        <div class=\"row g-3 mb-3\">
            <div class=\"col-12\">
                <article class=\"card-soft p-3 p-md-4 chart-panel stats-panel\">
                    <h2 class=\"h6 mb-3\">Threads Created (Last 30 Days)</h2>
                    <div class=\"chart-wrap chart-wrap-line\">
                        <canvas id=\"threadsLineChart\"></canvas>
                    </div>
                </article>
            </div>
        </div>

        <div class=\"row g-3 mb-3\">
            <div class=\"col-12\">
                <article class=\"card-soft p-3 p-md-4 chart-panel stats-panel\">
                    <h2 class=\"h6 mb-3\">Top 10 Most Active Users</h2>
                    <div class=\"chart-wrap chart-wrap-bar\">
                        <canvas id=\"topUsersBarChart\"></canvas>
                    </div>
                </article>
            </div>
        </div>

        <div class=\"row g-3\">
            <div class=\"col-lg-6\">
                <article class=\"card-soft p-3 p-md-4 h-100 stats-panel\">
                    <h2 class=\"h6 mb-3\">User Activity</h2>
                    <div class=\"row g-3\">
                        <div class=\"col-sm-6\">
                            <div class=\"metric-tile metric-tile-green\">
                                <div class=\"metric-label\">Active Today</div>
                                <div class=\"metric-value\">";
        // line 102
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 102, $this->source); })()), "activeUsersToday", [], "any", false, false, false, 102), "html", null, true);
        yield " users</div>
                            </div>
                        </div>
                        <div class=\"col-sm-6\">
                            <div class=\"metric-tile metric-tile-blue\">
                                <div class=\"metric-label\">Active This Week</div>
                                <div class=\"metric-value\">";
        // line 108
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 108, $this->source); })()), "activeUsersThisWeek", [], "any", false, false, false, 108), "html", null, true);
        yield " users</div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
            <div class=\"col-lg-6\">
                <article class=\"card-soft p-3 p-md-4 h-100 stats-panel\">
                    <h2 class=\"h6 mb-3\">Interactions</h2>
                    <div class=\"row g-3\">
                        <div class=\"col-sm-4\">
                            <div class=\"metric-tile metric-tile-green\">
                                <div class=\"metric-label\">Total Likes</div>
                                <div class=\"metric-value\">";
        // line 121
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 121, $this->source); })()), "totalLikes", [], "any", false, false, false, 121), "html", null, true);
        yield "</div>
                            </div>
                        </div>
                        <div class=\"col-sm-4\">
                            <div class=\"metric-tile metric-tile-red\">
                                <div class=\"metric-label\">Total Dislikes</div>
                                <div class=\"metric-value\">";
        // line 127
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 127, $this->source); })()), "totalDislikes", [], "any", false, false, false, 127), "html", null, true);
        yield "</div>
                            </div>
                        </div>
                        <div class=\"col-sm-4\">
                            <div class=\"metric-tile metric-tile-orange\">
                                <div class=\"metric-label\">Total Follows</div>
                                <div class=\"metric-value\">";
        // line 133
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 133, $this->source); })()), "totalFollows", [], "any", false, false, false, 133), "html", null, true);
        yield "</div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>
</main>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 144
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

        // line 145
        yield "    ";
        yield from $this->yieldParentBlock("javascripts", $context, $blocks);
        yield "
    <script src=\"";
        // line 146
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("vendor/chartjs/chart.umd.min.js"), "html", null, true);
        yield "\"></script>
    <script>
        const initStatisticsCharts = () => {
            if (typeof Chart === 'undefined') {
                document.querySelectorAll('.chart-wrap').forEach((wrap) => {
                    wrap.innerHTML = '<div class=\"chart-error\">Charts could not load (Chart.js unavailable). Check internet/CDN access.</div>';
                });
                return;
            }

            const statusLabels = ['Open', 'Locked', 'Archived'];
            const statusValues = [";
        // line 157
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 157, $this->source); })()), "openThreads", [], "any", false, false, false, 157), "html", null, true);
        yield ", ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 157, $this->source); })()), "lockedThreads", [], "any", false, false, false, 157), "html", null, true);
        yield ", ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 157, $this->source); })()), "archivedThreads", [], "any", false, false, false, 157), "html", null, true);
        yield "];

            const typeLabels = ['Discussion', 'Question', 'Announcement'];
            const typeValues = [";
        // line 160
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 160, $this->source); })()), "discussionThreads", [], "any", false, false, false, 160), "html", null, true);
        yield ", ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 160, $this->source); })()), "questionThreads", [], "any", false, false, false, 160), "html", null, true);
        yield ", ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 160, $this->source); })()), "announcementThreads", [], "any", false, false, false, 160), "html", null, true);
        yield "];

            const threadDayLabels = ";
        // line 162
        yield json_encode(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 162, $this->source); })()), "threadsPerDayLabels", [], "any", false, false, false, 162));
        yield ";
            const threadDayValues = ";
        // line 163
        yield json_encode(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 163, $this->source); })()), "threadsPerDayValues", [], "any", false, false, false, 163));
        yield ";

            const topUserLabels = ";
        // line 165
        yield json_encode(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 165, $this->source); })()), "topUsersLabels", [], "any", false, false, false, 165));
        yield ";
            const topUserValues = ";
        // line 166
        yield json_encode(CoreExtension::getAttribute($this->env, $this->source, (isset($context["stats"]) || array_key_exists("stats", $context) ? $context["stats"] : (function () { throw new RuntimeError('Variable "stats" does not exist.', 166, $this->source); })()), "topUsersValues", [], "any", false, false, false, 166));
        yield ";

            const rankingColors = topUserValues.map((_, index) => {
                if (index === 0) return '#FFD700';
                if (index === 1) return '#C0C0C0';
                if (index === 2) return '#CD7F32';
                return '#2196F3';
            });

            const statusEl = document.getElementById('statusPieChart');
            const typeEl = document.getElementById('typePieChart');
            const lineEl = document.getElementById('threadsLineChart');
            const barEl = document.getElementById('topUsersBarChart');

            if (!statusEl || !typeEl || !lineEl || !barEl) {
                return;
            }

            // Prevent duplicate chart instances on repeated page lifecycle events (e.g. Turbo navigation).
            [statusEl, typeEl, lineEl, barEl].forEach((el) => {
                const chart = Chart.getChart(el);
                if (chart) {
                    chart.destroy();
                }
            });

            new Chart(statusEl, {
                type: 'pie',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusValues,
                        backgroundColor: ['#4CAF50', '#FF9800', '#9E9E9E'],
                        borderColor: '#ffffff',
                        borderWidth: 3,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                    },
                },
            });

            new Chart(typeEl, {
                type: 'pie',
                data: {
                    labels: typeLabels,
                    datasets: [{
                        data: typeValues,
                        backgroundColor: ['#2196F3', '#FF5722', '#9C27B0'],
                        borderColor: '#ffffff',
                        borderWidth: 3,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                    },
                },
            });

            new Chart(lineEl, {
                type: 'line',
                data: {
                    labels: threadDayLabels,
                    datasets: [{
                        label: 'Threads',
                        data: threadDayValues,
                        borderColor: '#2196F3',
                        backgroundColor: 'rgba(33, 150, 243, 0.15)',
                        pointBackgroundColor: '#2196F3',
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        tension: 0.25,
                        fill: true,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grace: 1,
                            ticks: {
                                precision: 0,
                            },
                        },
                    },
                },
            });

            new Chart(barEl, {
                type: 'bar',
                data: {
                    labels: topUserLabels,
                    datasets: [{
                        label: 'Activity',
                        data: topUserValues,
                        backgroundColor: rankingColors,
                        borderRadius: 8,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grace: 1,
                            ticks: {
                                precision: 0,
                            },
                        },
                    },
                },
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initStatisticsCharts, { once: true });
        } else {
            initStatisticsCharts();
        }

        document.addEventListener('turbo:load', initStatisticsCharts);
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
        return "admin/statistics.html.twig";
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
        return array (  345 => 166,  341 => 165,  336 => 163,  332 => 162,  323 => 160,  313 => 157,  299 => 146,  294 => 145,  281 => 144,  260 => 133,  251 => 127,  242 => 121,  226 => 108,  217 => 102,  160 => 48,  150 => 41,  140 => 34,  130 => 27,  118 => 18,  108 => 11,  101 => 6,  88 => 5,  65 => 3,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}Forum Statistics{% endblock %}

{% block body %}
<main class=\"page-shell\">
    <div class=\"container stats-shell stats-fxml-theme\">
        <section class=\"card-soft p-3 p-md-4 mb-3 stats-head stats-panel\">
            <div class=\"d-flex flex-wrap justify-content-between align-items-center gap-2\">
                <div class=\"d-flex align-items-center gap-2 gap-md-3\">
                    <a class=\"btn btn-stats-back\" href=\"{{ path('app_admin_forum') }}\">Back</a>
                    <div>
                        <p class=\"mb-1 section-kicker text-uppercase\">Analytics Dashboard</p>
                        <h1 class=\"h3 m-0 stats-title\">Forum Statistics</h1>
                        <p class=\"text-muted small mb-0\">Live overview of forum health, activity, and engagement</p>
                    </div>
                </div>
                <a class=\"btn btn-stats-refresh\" href=\"{{ path('app_admin_statistics') }}\">Refresh</a>
            </div>
        </section>

        <div class=\"row g-3 mb-3\">
            <div class=\"col-6 col-lg-3\">
                <article class=\"card-soft p-3 h-100 stat-card stat-card-blue stats-panel\">
                    <div class=\"stat-icon mb-2\"><i class=\"ni ni-collection\"></i></div>
                    <div class=\"stat-label\">Total Threads</div>
                    <div class=\"stat-value\">{{ stats.totalThreads }}</div>
                </article>
            </div>
            <div class=\"col-6 col-lg-3\">
                <article class=\"card-soft p-3 h-100 stat-card stat-card-green stats-panel\">
                    <div class=\"stat-icon mb-2\"><i class=\"ni ni-chat-round\"></i></div>
                    <div class=\"stat-label\">Total Replies</div>
                    <div class=\"stat-value\">{{ stats.totalReplies }}</div>
                </article>
            </div>
            <div class=\"col-6 col-lg-3\">
                <article class=\"card-soft p-3 h-100 stat-card stat-card-orange stats-panel\">
                    <div class=\"stat-icon mb-2\"><i class=\"ni ni-single-02\"></i></div>
                    <div class=\"stat-label\">Total Users</div>
                    <div class=\"stat-value\">{{ stats.totalUsers }}</div>
                </article>
            </div>
            <div class=\"col-6 col-lg-3\">
                <article class=\"card-soft p-3 h-100 stat-card stat-card-violet stats-panel\">
                    <div class=\"stat-icon mb-2\"><i class=\"ni ni-bullet-list-67\"></i></div>
                    <div class=\"stat-label\">Total Categories</div>
                    <div class=\"stat-value\">{{ stats.totalCategories }}</div>
                </article>
            </div>
        </div>

        <div class=\"row g-3 mb-3\">
            <div class=\"col-lg-6\">
                <article class=\"card-soft p-3 p-md-4 h-100 chart-panel stats-panel\">
                    <h2 class=\"h6 mb-3\">Thread Status Distribution</h2>
                    <div class=\"chart-wrap chart-wrap-pie\">
                        <canvas id=\"statusPieChart\"></canvas>
                    </div>
                </article>
            </div>
            <div class=\"col-lg-6\">
                <article class=\"card-soft p-3 p-md-4 h-100 chart-panel stats-panel\">
                    <h2 class=\"h6 mb-3\">Thread Type Distribution</h2>
                    <div class=\"chart-wrap chart-wrap-pie\">
                        <canvas id=\"typePieChart\"></canvas>
                    </div>
                </article>
            </div>
        </div>

        <div class=\"row g-3 mb-3\">
            <div class=\"col-12\">
                <article class=\"card-soft p-3 p-md-4 chart-panel stats-panel\">
                    <h2 class=\"h6 mb-3\">Threads Created (Last 30 Days)</h2>
                    <div class=\"chart-wrap chart-wrap-line\">
                        <canvas id=\"threadsLineChart\"></canvas>
                    </div>
                </article>
            </div>
        </div>

        <div class=\"row g-3 mb-3\">
            <div class=\"col-12\">
                <article class=\"card-soft p-3 p-md-4 chart-panel stats-panel\">
                    <h2 class=\"h6 mb-3\">Top 10 Most Active Users</h2>
                    <div class=\"chart-wrap chart-wrap-bar\">
                        <canvas id=\"topUsersBarChart\"></canvas>
                    </div>
                </article>
            </div>
        </div>

        <div class=\"row g-3\">
            <div class=\"col-lg-6\">
                <article class=\"card-soft p-3 p-md-4 h-100 stats-panel\">
                    <h2 class=\"h6 mb-3\">User Activity</h2>
                    <div class=\"row g-3\">
                        <div class=\"col-sm-6\">
                            <div class=\"metric-tile metric-tile-green\">
                                <div class=\"metric-label\">Active Today</div>
                                <div class=\"metric-value\">{{ stats.activeUsersToday }} users</div>
                            </div>
                        </div>
                        <div class=\"col-sm-6\">
                            <div class=\"metric-tile metric-tile-blue\">
                                <div class=\"metric-label\">Active This Week</div>
                                <div class=\"metric-value\">{{ stats.activeUsersThisWeek }} users</div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
            <div class=\"col-lg-6\">
                <article class=\"card-soft p-3 p-md-4 h-100 stats-panel\">
                    <h2 class=\"h6 mb-3\">Interactions</h2>
                    <div class=\"row g-3\">
                        <div class=\"col-sm-4\">
                            <div class=\"metric-tile metric-tile-green\">
                                <div class=\"metric-label\">Total Likes</div>
                                <div class=\"metric-value\">{{ stats.totalLikes }}</div>
                            </div>
                        </div>
                        <div class=\"col-sm-4\">
                            <div class=\"metric-tile metric-tile-red\">
                                <div class=\"metric-label\">Total Dislikes</div>
                                <div class=\"metric-value\">{{ stats.totalDislikes }}</div>
                            </div>
                        </div>
                        <div class=\"col-sm-4\">
                            <div class=\"metric-tile metric-tile-orange\">
                                <div class=\"metric-label\">Total Follows</div>
                                <div class=\"metric-value\">{{ stats.totalFollows }}</div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>
</main>
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    <script src=\"{{ asset('vendor/chartjs/chart.umd.min.js') }}\"></script>
    <script>
        const initStatisticsCharts = () => {
            if (typeof Chart === 'undefined') {
                document.querySelectorAll('.chart-wrap').forEach((wrap) => {
                    wrap.innerHTML = '<div class=\"chart-error\">Charts could not load (Chart.js unavailable). Check internet/CDN access.</div>';
                });
                return;
            }

            const statusLabels = ['Open', 'Locked', 'Archived'];
            const statusValues = [{{ stats.openThreads }}, {{ stats.lockedThreads }}, {{ stats.archivedThreads }}];

            const typeLabels = ['Discussion', 'Question', 'Announcement'];
            const typeValues = [{{ stats.discussionThreads }}, {{ stats.questionThreads }}, {{ stats.announcementThreads }}];

            const threadDayLabels = {{ stats.threadsPerDayLabels|json_encode|raw }};
            const threadDayValues = {{ stats.threadsPerDayValues|json_encode|raw }};

            const topUserLabels = {{ stats.topUsersLabels|json_encode|raw }};
            const topUserValues = {{ stats.topUsersValues|json_encode|raw }};

            const rankingColors = topUserValues.map((_, index) => {
                if (index === 0) return '#FFD700';
                if (index === 1) return '#C0C0C0';
                if (index === 2) return '#CD7F32';
                return '#2196F3';
            });

            const statusEl = document.getElementById('statusPieChart');
            const typeEl = document.getElementById('typePieChart');
            const lineEl = document.getElementById('threadsLineChart');
            const barEl = document.getElementById('topUsersBarChart');

            if (!statusEl || !typeEl || !lineEl || !barEl) {
                return;
            }

            // Prevent duplicate chart instances on repeated page lifecycle events (e.g. Turbo navigation).
            [statusEl, typeEl, lineEl, barEl].forEach((el) => {
                const chart = Chart.getChart(el);
                if (chart) {
                    chart.destroy();
                }
            });

            new Chart(statusEl, {
                type: 'pie',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusValues,
                        backgroundColor: ['#4CAF50', '#FF9800', '#9E9E9E'],
                        borderColor: '#ffffff',
                        borderWidth: 3,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                    },
                },
            });

            new Chart(typeEl, {
                type: 'pie',
                data: {
                    labels: typeLabels,
                    datasets: [{
                        data: typeValues,
                        backgroundColor: ['#2196F3', '#FF5722', '#9C27B0'],
                        borderColor: '#ffffff',
                        borderWidth: 3,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                    },
                },
            });

            new Chart(lineEl, {
                type: 'line',
                data: {
                    labels: threadDayLabels,
                    datasets: [{
                        label: 'Threads',
                        data: threadDayValues,
                        borderColor: '#2196F3',
                        backgroundColor: 'rgba(33, 150, 243, 0.15)',
                        pointBackgroundColor: '#2196F3',
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        tension: 0.25,
                        fill: true,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grace: 1,
                            ticks: {
                                precision: 0,
                            },
                        },
                    },
                },
            });

            new Chart(barEl, {
                type: 'bar',
                data: {
                    labels: topUserLabels,
                    datasets: [{
                        label: 'Activity',
                        data: topUserValues,
                        backgroundColor: rankingColors,
                        borderRadius: 8,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grace: 1,
                            ticks: {
                                precision: 0,
                            },
                        },
                    },
                },
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initStatisticsCharts, { once: true });
        } else {
            initStatisticsCharts();
        }

        document.addEventListener('turbo:load', initStatisticsCharts);
    </script>
{% endblock %}
", "admin/statistics.html.twig", "C:\\Users\\saifd\\Documents\\serintiy\\serinity-web\\templates\\admin\\statistics.html.twig");
    }
}
