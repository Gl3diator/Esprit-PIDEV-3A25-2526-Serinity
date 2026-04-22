<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Service\User\CoachInsightService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Environment;

#[Route('/user/exercises/coach')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class ExerciseCoachController extends AbstractUserUiController
{
    private const SESSION_COACH_REPORT_KEY = 'serinity.latest_coach_report';

    public function __construct(
        private readonly CoachInsightService $coachInsightService,
        private readonly Environment $twig,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Route('', name: 'user_ui_exercises_coach', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $user = $this->currentUser();
        $this->logger->info('Exercise coach page opened.', [
            'user_id' => $user->getId(),
            'user_email' => $user->getEmail(),
        ]);

        $coach = $this->coachInsightService->getInsight($user);
        $request->getSession()->set(self::SESSION_COACH_REPORT_KEY, $coach);

        return $this->render('user/pages/exercise_coach.html.twig', [
            'nav' => $this->buildNav('user_ui_exercises'),
            'userName' => $user->getEmail(),
            'report' => $coach['report'],
            'insight' => $coach['insight'],
        ]);
    }

    #[Route('/pdf', name: 'user_ui_exercises_coach_pdf', methods: ['GET'])]
    public function pdf(Request $request): Response
    {
        $user = $this->currentUser();
        $coach = $request->getSession()->get(self::SESSION_COACH_REPORT_KEY);
        if (!is_array($coach) || !is_array($coach['report'] ?? null) || !is_array($coach['insight'] ?? null)) {
            $this->logger->warning('Coach PDF generation skipped because no generated report was found in the session.', [
                'user_id' => $user->getId(),
            ]);
            $this->addFlash('error', 'Unable to generate the PDF report. Please open the Coach page and try again.');

            return $this->redirectToRoute('user_ui_exercises_coach');
        }

        if (!$this->hasSendableInsight($coach['insight'])) {
            $this->logger->warning('Coach PDF generation skipped because stored insight content is empty.', [
                'user_id' => $user->getId(),
            ]);
            $this->addFlash('error', 'Unable to generate the PDF report. Please open the Coach page and try again.');

            return $this->redirectToRoute('user_ui_exercises_coach');
        }

        $safeReport = $this->sanitizePdfValue($coach['report']);
        $safeInsight = $this->sanitizePdfValue($coach['insight']);
        $html = $this->twig->render('user/coach/report_pdf.html.twig', [
            'userEmail' => $this->sanitizePdfText($user->getEmail()),
            'report' => is_array($safeReport) ? $safeReport : [],
            'insight' => is_array($safeInsight) ? $safeInsight : [],
            'generatedAt' => new \DateTimeImmutable(),
        ]);

        $response = new Response($this->buildPdfOutputSafely($html));
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'serinity-coaching-report.pdf',
        ));

        return $response;
    }

    private function buildPdfOutputSafely(string $html): string
    {
        $previousHandler = set_error_handler(static function (int $severity, string $message, string $file = '', int $line = 0) use (&$previousHandler): bool {
            if (str_contains($message, 'iconv(): Detected an incomplete multibyte character in input string')) {
                return true;
            }

            if (is_callable($previousHandler)) {
                return (bool) $previousHandler($severity, $message, $file, $line);
            }

            return false;
        });

        try {
            $options = new Options();
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('isRemoteEnabled', false);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            return $dompdf->output();
        } finally {
            restore_error_handler();
        }
    }

    /** @param array<string,mixed> $insight */
    private function hasSendableInsight(array $insight): bool
    {
        return is_string($insight['summary'] ?? null)
            && trim($insight['summary']) !== ''
            && is_array($insight['strengths'] ?? null)
            && is_array($insight['improvements'] ?? null)
            && is_array($insight['recommendations'] ?? null)
            && is_array($insight['plan7Days'] ?? null)
            && is_array($insight['nutritionSupport'] ?? null);
    }

    private function sanitizePdfValue(mixed $value): mixed
    {
        if (is_string($value)) {
            return $this->sanitizePdfText($value);
        }

        if (!is_array($value)) {
            return $value;
        }

        foreach ($value as $key => $nestedValue) {
            $value[$key] = $this->sanitizePdfValue($nestedValue);
        }

        return $value;
    }

    private function sanitizePdfText(string $text): string
    {
        $text = trim($text);
        if ($text === '') {
            return '';
        }

        $text = strtr($text, [
            "\u{2018}" => "'",
            "\u{2019}" => "'",
            "\u{201A}" => "'",
            "\u{201B}" => "'",
            "\u{201C}" => '"',
            "\u{201D}" => '"',
            "\u{201E}" => '"',
            "\u{201F}" => '"',
            "\u{2013}" => '-',
            "\u{2014}" => '-',
            "\u{2015}" => '-',
            "\u{2026}" => '...',
            "\u{00A0}" => ' ',
            "\u{00B7}" => '-',
        ]);

        $text = preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $text) ?? $text;
        $text = preg_replace('/[ \t]+/', ' ', $text) ?? $text;

        return trim($text);
    }
}
