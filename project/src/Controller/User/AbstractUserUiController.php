<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Enum\AccountStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractUserUiController extends AbstractController
{
    protected function currentUser(): User
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if (!in_array($user->getRole(), ['PATIENT', 'THERAPIST'], true)) {
            throw $this->createAccessDeniedException();
        }

        if ($user->getAccountStatus() === AccountStatus::DISABLED->value) {
            throw $this->createAccessDeniedException('Your account is disabled.');
        }

        return $user;
    }

    /**
     * @return list<array{
     *     label:string,
     *     route:string,
     *     icon:string,
     *     section:string,
     *     active:bool,
     *     children?:list<array{label:string,route:string,icon:string,active:bool}>
     * }>
     */
    protected function buildNav(string $activeRoute): array
    {
        $user = $this->currentUser();
        $role = $user->getRole();

        $items = [
            ['label' => 'Dashboard', 'route' => 'user_ui_dashboard', 'icon' => 'dashboard', 'section' => 'home'],
            ['label' => 'Profile', 'route' => 'user_ui_profile', 'icon' => 'person', 'section' => 'home'],
            ['label' => 'Settings', 'route' => 'user_ui_settings', 'icon' => 'settings', 'section' => 'home'],
        ];

        /**
         * MODULES SELON ROLE
         */
        if ($role === 'PATIENT') {
            $items[] = [
                'label' => 'Doctors',
                'route' => 'app_doctors',
                'icon' => 'medical_services',
                'section' => 'modules'
            ];

            $items[] = [
                'label' => 'Mes rendez vous',
                'route' => 'app_patient_rdv',
                'icon' => 'calendar_month',
                'section' => 'modules'
            ];
        }

        if ($role === 'THERAPIST') {
            $items[] = [
                'label' => 'Gestion des rendez vous',
                'route' => 'app_therapist_rdv',
                'icon' => 'calendar_month',
                'section' => 'modules'
            ];

          
        }

        /**
         * COMMUNS
         */
        $items[] = [
            'label' => 'Exercises',
            'route' => 'user_ui_exercises',
            'icon' => 'fitness_center',
            'section' => 'modules'
        ];

        $items[] = [
            'label' => 'Forum',
            'route' => 'user_ui_forum',
            'icon' => 'forum',
            'section' => 'modules'
        ];

        $items[] = [
            'label' => 'Mood',
            'route' => 'user_ui_mood',
            'icon' => 'mood',
            'section' => 'modules',
            'children' => [
                ['label' => 'Mood entries', 'route' => 'user_ui_mood', 'icon' => 'list'],
                ['label' => 'Journal', 'route' => 'user_ui_journal_entry', 'icon' => 'edit_note'],
                ['label' => 'Insights', 'route' => 'user_ui_mood_insights', 'icon' => 'insights'],
                ['label' => 'Recovery plan', 'route' => 'user_ui_mood_recovery_plan', 'icon' => 'healing'],
            ],
        ];

        $items[] = [
            'label' => 'Sleep',
            'route' => 'user_ui_sommeil_list',
            'icon' => 'bedtime',
            'section' => 'modules',
            'children' => [
                ['label' => 'Sommail', 'route' => 'user_ui_sommeil_list', 'icon' => 'bedtime'],
                ['label' => 'Reves management', 'route' => 'user_ui_reve_index', 'icon' => 'nights_stay'],
            ],
        ];

        return array_map(static function (array $item) use ($activeRoute): array {
            $children = $item['children'] ?? [];

            $mappedChildren = array_map(
                static fn(array $child): array => [
                    ...$child,
                    'active' => $child['route'] === $activeRoute,
                ],
                $children
            );

            return [
                ...$item,
                'children' => $mappedChildren,
                'active' => $item['route'] === $activeRoute
                    || array_any($mappedChildren, static fn(array $child): bool => $child['active']),
            ];
        }, $items);
    }
}