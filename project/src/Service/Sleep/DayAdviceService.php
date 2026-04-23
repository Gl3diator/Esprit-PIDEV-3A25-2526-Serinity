<?php

namespace App\Service\Sleep;

class DayAdviceService
{
    private const HUMEUR_CATEGORIE = [
        '😄 Joyeux' => 'joyeux',
        '😌 Serein' => 'paisible',
        '😢 Triste' => 'triste',
        '😨 Effrayé' => 'effrayé',
        '😐 Neutre' => 'neutre',
    ];

    public function getAdvice(array $hfResult, array $reve): array
    {
        $humeur = trim((string) ($reve['humeur'] ?? ''));
        $typeReve = trim((string) ($reve['type_reve'] ?? ''));

        $categorie = self::HUMEUR_CATEGORIE[$humeur] ?? 'neutre';
        $sentiment = trim((string) ($hfResult['sentiment'] ?? 'neutre'));
        $confiance = (int) ($hfResult['confiance'] ?? 0);

        $mixte = $categorie === 'joyeux'
            && $sentiment === 'négatif'
            && $confiance > 60;

        switch ($categorie) {
            case 'joyeux':
                $titre = $mixte
                    ? 'Bonne humeur mais restez attentif 🌤️'
                    : 'Belle énergie ce matin ! 🌟';
                $classe = 'success';
                $emoji = '😊';
                $conseils = [
                    '☀️ Profitez de cette bonne humeur — planifiez une tâche importante.',
                    '🏃 Idéal pour faire du sport ou une activité créative.',
                    '🤝 Bonne journée pour les interactions sociales et réunions.',
                    '📝 Notez vos idées ce matin, votre créativité est au maximum.',
                    '🎯 C’est le bon moment pour attaquer vos projets difficiles.',
                ];
                break;

            case 'paisible':
                $titre = 'Réveil serein, journée apaisée 🌿';
                $classe = 'success';
                $emoji = '😌';
                $conseils = [
                    '🧘 Prenez le temps d’un moment calme avant de commencer.',
                    '📖 Journaling ce matin pour ancrer cette sérénité.',
                    '🌿 Évitez les situations stressantes inutiles aujourd’hui.',
                    '🎵 Musique douce en fond pendant le travail.',
                    '🍃 Une balade en nature si possible — profitez de cette paix.',
                ];
                break;

            case 'triste':
                $titre = 'Journée à traverser avec douceur 🌧️';
                $classe = 'warning';
                $emoji = '😔';
                $conseils = [
                    '🧘 5 min de respiration profonde avant de commencer.',
                    '🍵 Petit-déjeuner chaud et nourrissant.',
                    '📋 Limitez les décisions importantes à cet après-midi.',
                    '🚶 Une marche de 10 min peut changer votre état.',
                    '📵 Évitez les réseaux sociaux le matin.',
                    '💬 Parlez à quelqu’un de bienveillant aujourd’hui.',
                ];
                break;

            case 'effrayé':
                $titre = 'Reconnecter avec le calme et la sécurité 🛡️';
                $classe = 'danger';
                $emoji = '😨';
                $conseils = [
                    '🛡️ Rappelez-vous : vous êtes en sécurité, c’était un rêve.',
                    '🧘 Ancrage : nommez 5 choses que vous voyez autour de vous.',
                    '🍵 Une boisson chaude pour apaiser le système nerveux.',
                    '💡 Restez dans un espace lumineux et confortable.',
                    '🤝 Entourez-vous de personnes bienveillantes aujourd’hui.',
                    '🌙 Ce soir : évitez les contenus anxiogènes avant de dormir.',
                ];
                break;

            default:
                $titre = 'Journée équilibrée 🌤️';
                $classe = 'secondary';
                $emoji = '😐';
                $conseils = [
                    '📅 Journée idéale pour les tâches de routine.',
                    '🎯 Fixez-vous 3 objectifs clairs pour aujourd’hui.',
                    '💧 Pensez à bien vous hydrater.',
                ];
                break;
        }

        if ($typeReve === 'Cauchemar') {
            $conseils[] = '🌙 Cauchemar cette nuit — couchez-vous 30 min plus tôt ce soir.';
            $conseils[] = '📖 Lecture légère avant de dormir plutôt qu’un écran.';
        }

        if ($typeReve === 'Lucide') {
            $conseils[] = '🧠 Rêve lucide — votre cerveau est très actif, profitez-en.';
        }

        $confidenceNote = $confiance > 0
            ? "Analyse IA : sentiment {$sentiment} ({$confiance}% de confiance)"
            : null;

        return [
            'titre' => $titre,
            'classe' => $classe,
            'emoji' => $emoji,
            'conseils' => array_values(array_unique($conseils)),
            'confidenceNote' => $confidenceNote,
        ];
    }
}