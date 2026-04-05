<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\ForumThread;
use App\Entity\Notification;
use App\Entity\PostInteraction;
use App\Entity\Reply;
use App\Enum\NotificationType;
use App\Enum\ThreadStatus;
use App\Enum\ThreadType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $adminId = '11111111-1111-4111-8111-111111111111';
        $aliceId = '22222222-2222-4222-8222-222222222222';
        $bobId = '33333333-3333-4333-8333-333333333333';

        $connection = $manager->getConnection();
        $connection->executeStatement('DELETE FROM profiles');
        $connection->insert('profiles', [
            'user_id' => $adminId,
            'email' => 'admin@serinity.local',
            'username' => 'admin',
            'roles' => 'admin',
            'password' => password_hash('admin123', PASSWORD_BCRYPT),
        ]);
        $connection->insert('profiles', [
            'user_id' => $aliceId,
            'email' => 'alice@serinity.local',
            'username' => 'alice',
            'roles' => 'client',
            'password' => password_hash('alice123', PASSWORD_BCRYPT),
        ]);
        $connection->insert('profiles', [
            'user_id' => $bobId,
            'email' => 'bob@serinity.local',
            'username' => 'bob',
            'roles' => 'client',
            'password' => password_hash('bob123', PASSWORD_BCRYPT),
        ]);

        $general = new Category();
        $general->setName('General Support');
        $general->setSlug('general-support');
        $general->setDescription('General community discussions and support topics.');
        $manager->persist($general);

        $sleep = new Category();
        $sleep->setName('Sleep & Recovery');
        $sleep->setSlug('sleep-recovery');
        $sleep->setDescription('Questions, tips, and routines around healthy sleep.');
        $manager->persist($sleep);

        $mood = new Category();
        $mood->setName('Mood Tracking');
        $mood->setSlug('mood-tracking');
        $mood->setDescription('Mood reflection threads and emotional wellness checks.');
        $mood->setParent($general);
        $manager->persist($mood);

        $thread1 = new ForumThread();
        $thread1->setAuthorId($aliceId);
        $thread1->setCategory($general);
        $thread1->setTitle('How do you maintain calm under pressure?');
        $thread1->setContent('I am preparing for exams and work deadlines at the same time. I would like practical methods to stay calm, focused, and avoid burnout throughout the week.');
        $thread1->setType(ThreadType::DISCUSSION);
        $thread1->setStatus(ThreadStatus::OPEN);
        $thread1->setIsPinned(true);
        $manager->persist($thread1);

        $thread2 = new ForumThread();
        $thread2->setAuthorId($bobId);
        $thread2->setCategory($sleep);
        $thread2->setTitle('Best evening routine to improve sleep quality?');
        $thread2->setContent('I sleep late and wake up tired. I want a realistic evening routine that helps me sleep faster and wake up feeling rested.');
        $thread2->setType(ThreadType::QUESTION);
        $thread2->setStatus(ThreadStatus::OPEN);
        $manager->persist($thread2);

        $reply1 = new Reply();
        $reply1->setThread($thread1);
        $reply1->setAuthorId($adminId);
        $reply1->setContent('Try a 10 minute breathing reset every 90 minutes and keep your priorities visible as a short checklist.');
        $manager->persist($reply1);

        $reply2 = new Reply();
        $reply2->setThread($thread2);
        $reply2->setAuthorId($aliceId);
        $reply2->setContent('Reduce screen time one hour before bed and avoid caffeine after lunch. This helped me a lot.');
        $manager->persist($reply2);

        $thread1->setReplyCount(1);
        $thread2->setReplyCount(1);

        $interaction1 = new PostInteraction();
        $interaction1->setThread($thread1);
        $interaction1->setUserId($bobId);
        $interaction1->setVote(1);
        $interaction1->setFollow(true);
        $manager->persist($interaction1);

        $interaction2 = new PostInteraction();
        $interaction2->setThread($thread1);
        $interaction2->setUserId($adminId);
        $interaction2->setVote(1);
        $manager->persist($interaction2);

        $thread1->setLikeCount(2);
        $thread1->setDislikeCount(0);
        $thread1->setFollowCount(1);

        $notification = new Notification();
        $notification->setThread($thread1);
        $notification->setRecipientId($aliceId);
        $notification->setType(NotificationType::LIKE);
        $notification->setContent('bob liked your thread "How do you maintain calm under pressure?"');
        $manager->persist($notification);

        $manager->flush();
    }
}
