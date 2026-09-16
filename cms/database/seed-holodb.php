<?php

/**
 * Preview PolarIS + phpretro_* sqlite. Run outside Laravel so HolodbWriteGuard
 * never sees CREATE/INSERT. Never creates users_settings.
 */

$path = __DIR__.'/holodb.sqlite';
if (file_exists($path)) {
    unlink($path);
}

$pdo = new PDO('sqlite:'.$path);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('PRAGMA journal_mode = WAL');
$pdo->exec('PRAGMA foreign_keys = OFF');

$pdo->exec(<<<'SQL'
CREATE TABLE users (
  id INTEGER PRIMARY KEY,
  username TEXT NOT NULL UNIQUE,
  real_name TEXT NOT NULL DEFAULT '',
  password TEXT NOT NULL,
  mail TEXT NOT NULL DEFAULT '',
  mail_verified TEXT NOT NULL DEFAULT '1',
  account_created INTEGER NOT NULL DEFAULT 0,
  account_day_of_birth INTEGER NOT NULL DEFAULT 0,
  last_login INTEGER NOT NULL DEFAULT 0,
  last_online INTEGER NOT NULL DEFAULT 0,
  motto TEXT NOT NULL DEFAULT '',
  look TEXT NOT NULL DEFAULT '',
  gender TEXT NOT NULL DEFAULT 'M',
  rank INTEGER NOT NULL DEFAULT 1,
  credits INTEGER NOT NULL DEFAULT 0,
  pixels INTEGER NOT NULL DEFAULT 0,
  points INTEGER NOT NULL DEFAULT 0,
  online TEXT NOT NULL DEFAULT '0',
  auth_ticket TEXT NOT NULL DEFAULT '',
  ip_register TEXT NOT NULL DEFAULT '',
  ip_current TEXT NOT NULL DEFAULT '',
  machine_id TEXT NOT NULL DEFAULT '',
  home_room INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE rooms (
  id INTEGER PRIMARY KEY,
  name TEXT NOT NULL DEFAULT '',
  owner_id INTEGER NOT NULL DEFAULT 0,
  owner_name TEXT NOT NULL DEFAULT '',
  users INTEGER NOT NULL DEFAULT 0,
  users_max INTEGER NOT NULL DEFAULT 25,
  is_public TEXT NOT NULL DEFAULT '0',
  is_staff_picked TEXT NOT NULL DEFAULT '0',
  tags TEXT NOT NULL DEFAULT '',
  description TEXT NOT NULL DEFAULT ''
);

CREATE TABLE hotelview_news (
  id INTEGER PRIMARY KEY,
  title TEXT NOT NULL DEFAULT '',
  text TEXT NOT NULL DEFAULT '',
  button_text TEXT NOT NULL DEFAULT '',
  button_type TEXT NOT NULL DEFAULT 'web',
  button_link TEXT NOT NULL DEFAULT '',
  image TEXT NOT NULL DEFAULT ''
);

CREATE TABLE phpretro_news (
  id INTEGER PRIMARY KEY,
  title TEXT NOT NULL,
  summary TEXT NOT NULL,
  story TEXT NOT NULL,
  author TEXT NOT NULL,
  categories TEXT NOT NULL DEFAULT '',
  images TEXT NOT NULL DEFAULT '',
  time INTEGER NOT NULL
);

CREATE TABLE phpretro_site_settings (
  setting_key TEXT PRIMARY KEY,
  setting_value TEXT NOT NULL,
  updated_by INTEGER,
  updated_at INTEGER NOT NULL
);

CREATE TABLE phpretro_campaigns (
  id INTEGER PRIMARY KEY,
  name TEXT NOT NULL DEFAULT '',
  "desc" TEXT NOT NULL DEFAULT '',
  image TEXT NOT NULL DEFAULT '',
  url TEXT NOT NULL DEFAULT '',
  visible TEXT NOT NULL DEFAULT '1',
  sort_order INTEGER NOT NULL DEFAULT 1
);

CREATE TABLE phpretro_faq (
  id INTEGER PRIMARY KEY,
  category TEXT NOT NULL DEFAULT 'general',
  question TEXT NOT NULL,
  answer TEXT NOT NULL,
  sort_order INTEGER NOT NULL DEFAULT 0,
  active INTEGER NOT NULL DEFAULT 1
);

CREATE TABLE phpretro_banners (
  id INTEGER PRIMARY KEY,
  text TEXT NOT NULL DEFAULT '',
  banner TEXT NOT NULL DEFAULT '',
  url TEXT NOT NULL DEFAULT '',
  status TEXT NOT NULL DEFAULT '1',
  advanced TEXT NOT NULL DEFAULT '0',
  html TEXT NOT NULL DEFAULT '',
  sort_order INTEGER NOT NULL DEFAULT 1
);

CREATE TABLE phpretro_myhabbo_layouts (
  id INTEGER PRIMARY KEY,
  user_id INTEGER NOT NULL,
  guild_id INTEGER NOT NULL DEFAULT 0,
  column_number INTEGER NOT NULL DEFAULT 1,
  widget_key TEXT NOT NULL,
  position INTEGER NOT NULL DEFAULT 0,
  visible INTEGER NOT NULL DEFAULT 1,
  privacy TEXT NOT NULL DEFAULT 'public'
);

CREATE TABLE phpretro_homes_catalogue (
  id INTEGER PRIMARY KEY,
  name TEXT NOT NULL DEFAULT '',
  description TEXT NOT NULL DEFAULT '',
  type TEXT NOT NULL DEFAULT 'sticker',
  data TEXT NOT NULL DEFAULT '',
  price INTEGER NOT NULL DEFAULT 0,
  amount INTEGER NOT NULL DEFAULT 1,
  category TEXT NOT NULL DEFAULT '',
  category_id INTEGER NOT NULL DEFAULT 0,
  min_rank INTEGER NOT NULL DEFAULT 1,
  placement TEXT NOT NULL DEFAULT 'anywhere'
);

CREATE TABLE phpretro_homes_items (
  id INTEGER PRIMARY KEY,
  user_id INTEGER NOT NULL,
  guild_id INTEGER NOT NULL DEFAULT 0,
  catalogue_id INTEGER NOT NULL,
  item_type TEXT NOT NULL DEFAULT 'sticker',
  skin TEXT NOT NULL DEFAULT '',
  data TEXT NOT NULL DEFAULT '',
  x INTEGER NOT NULL DEFAULT 0,
  y INTEGER NOT NULL DEFAULT 0,
  z INTEGER NOT NULL DEFAULT 1,
  placed INTEGER NOT NULL DEFAULT 1
);

CREATE TABLE phpretro_collectibles (
  id INTEGER PRIMARY KEY,
  name TEXT NOT NULL,
  description TEXT NOT NULL DEFAULT '',
  image TEXT NOT NULL DEFAULT '',
  time INTEGER NOT NULL
);

CREATE TABLE phpretro_recommended (
  id INTEGER PRIMARY KEY,
  rec_id INTEGER NOT NULL,
  type TEXT NOT NULL DEFAULT 'group',
  sponsered TEXT NOT NULL DEFAULT '0'
);

CREATE TABLE phpretro_user_reports (
  id INTEGER PRIMARY KEY,
  reporter_id INTEGER NOT NULL,
  reported_user_id INTEGER NOT NULL,
  reason TEXT NOT NULL DEFAULT '',
  evidence TEXT NOT NULL DEFAULT '',
  status TEXT NOT NULL DEFAULT 'open',
  assigned_to INTEGER,
  action_notes TEXT NOT NULL DEFAULT '',
  resolved_by INTEGER,
  resolved_at INTEGER,
  created_at INTEGER NOT NULL
);

CREATE TABLE phpretro_helpdesk_tickets (
  id INTEGER PRIMARY KEY,
  user_id INTEGER,
  username TEXT NOT NULL DEFAULT '',
  email TEXT NOT NULL DEFAULT '',
  ip TEXT NOT NULL DEFAULT '',
  subject TEXT NOT NULL DEFAULT '',
  message TEXT NOT NULL DEFAULT '',
  room_id INTEGER NOT NULL DEFAULT 0,
  status TEXT NOT NULL DEFAULT 'open',
  picked_by INTEGER,
  created_at INTEGER NOT NULL
);

CREATE TABLE bans (
  id INTEGER PRIMARY KEY,
  user_id INTEGER NOT NULL DEFAULT 0,
  ip TEXT NOT NULL DEFAULT '',
  machine_id TEXT NOT NULL DEFAULT '',
  user_staff_id INTEGER NOT NULL DEFAULT 0,
  timestamp INTEGER NOT NULL DEFAULT 0,
  ban_expire INTEGER NOT NULL DEFAULT 0,
  ban_reason TEXT NOT NULL DEFAULT '',
  type TEXT NOT NULL DEFAULT 'account',
  cfh_topic TEXT NOT NULL DEFAULT ''
);

CREATE TABLE chatlogs_room (
  id INTEGER PRIMARY KEY,
  user_from_id INTEGER NOT NULL DEFAULT 0,
  room_id INTEGER NOT NULL DEFAULT 0,
  timestamp INTEGER NOT NULL DEFAULT 0,
  message TEXT NOT NULL DEFAULT ''
);

CREATE TABLE catalog_items (
  id INTEGER PRIMARY KEY,
  catalog_name TEXT NOT NULL DEFAULT ''
);

CREATE TABLE guilds (
  id INTEGER PRIMARY KEY,
  user_id INTEGER NOT NULL DEFAULT 0,
  name TEXT NOT NULL DEFAULT '',
  description TEXT NOT NULL DEFAULT '',
  room_id INTEGER NOT NULL DEFAULT 0,
  state INTEGER NOT NULL DEFAULT 0,
  rights INTEGER NOT NULL DEFAULT 0,
  badge TEXT NOT NULL DEFAULT '',
  date_created INTEGER NOT NULL DEFAULT 0,
  forum INTEGER NOT NULL DEFAULT 1,
  read_forum TEXT NOT NULL DEFAULT 'EVERYONE',
  post_messages TEXT NOT NULL DEFAULT 'MEMBERS',
  post_threads TEXT NOT NULL DEFAULT 'MEMBERS',
  mod_forum TEXT NOT NULL DEFAULT 'ADMINS'
);

CREATE TABLE guilds_members (
  id INTEGER PRIMARY KEY,
  guild_id INTEGER NOT NULL,
  user_id INTEGER NOT NULL,
  level_id INTEGER NOT NULL DEFAULT 2
);

CREATE TABLE guilds_forums_threads (
  id INTEGER PRIMARY KEY,
  guild_id INTEGER NOT NULL,
  opener_id INTEGER NOT NULL DEFAULT 0,
  subject TEXT NOT NULL DEFAULT '',
  posts_count INTEGER NOT NULL DEFAULT 1,
  created_at INTEGER NOT NULL DEFAULT 0,
  updated_at INTEGER NOT NULL DEFAULT 0,
  state INTEGER NOT NULL DEFAULT 0,
  pinned INTEGER NOT NULL DEFAULT 0,
  locked INTEGER NOT NULL DEFAULT 0,
  admin_id INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE guilds_forums_comments (
  id INTEGER PRIMARY KEY,
  thread_id INTEGER NOT NULL,
  user_id INTEGER NOT NULL DEFAULT 0,
  message TEXT NOT NULL DEFAULT '',
  created_at INTEGER NOT NULL DEFAULT 0,
  state INTEGER NOT NULL DEFAULT 0,
  admin_id INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE messenger_friendships (
  id INTEGER PRIMARY KEY,
  user_one_id INTEGER NOT NULL,
  user_two_id INTEGER NOT NULL
);

CREATE TABLE vouchers (
  id INTEGER PRIMARY KEY,
  code TEXT NOT NULL DEFAULT '',
  credits INTEGER NOT NULL DEFAULT 0,
  points INTEGER NOT NULL DEFAULT 0,
  points_type INTEGER NOT NULL DEFAULT 0,
  catalog_item_id INTEGER NOT NULL DEFAULT 0,
  amount INTEGER NOT NULL DEFAULT 1,
  "limit" INTEGER NOT NULL DEFAULT -1
);

CREATE TABLE phpretro_admin_action_log (
  id INTEGER PRIMARY KEY,
  admin_id INTEGER NOT NULL,
  action_type TEXT NOT NULL DEFAULT '',
  target_type TEXT NOT NULL DEFAULT '',
  target_id INTEGER,
  details TEXT NOT NULL DEFAULT '',
  ip TEXT NOT NULL DEFAULT '',
  created_at INTEGER NOT NULL
);

CREATE TABLE phpretro_staff_sessions (
  id INTEGER PRIMARY KEY,
  user_id INTEGER NOT NULL,
  session_hash TEXT NOT NULL DEFAULT '',
  ip TEXT NOT NULL DEFAULT '',
  created_at INTEGER NOT NULL,
  last_activity INTEGER NOT NULL,
  revoked_at INTEGER
);

CREATE TABLE phpretro_staff_totp (
  user_id INTEGER PRIMARY KEY,
  secret_base32 TEXT NOT NULL DEFAULT '',
  enabled INTEGER NOT NULL DEFAULT 0,
  created_at INTEGER NOT NULL,
  verified_at INTEGER
);

CREATE TABLE phpretro_transactions (
  id INTEGER PRIMARY KEY,
  user_id INTEGER NOT NULL,
  type TEXT NOT NULL DEFAULT '',
  amount INTEGER NOT NULL DEFAULT 0,
  balance_after INTEGER NOT NULL DEFAULT 0,
  description TEXT NOT NULL DEFAULT '',
  reference_id TEXT NOT NULL DEFAULT '',
  created_at INTEGER NOT NULL
);

CREATE TABLE phpretro_myhabbo_guestbook (
  id INTEGER PRIMARY KEY,
  profile_user_id INTEGER NOT NULL,
  author_user_id INTEGER NOT NULL,
  message TEXT NOT NULL,
  created_at INTEGER NOT NULL
);

CREATE TABLE phpretro_group_guestbook (
  id INTEGER PRIMARY KEY,
  guild_id INTEGER NOT NULL,
  author_user_id INTEGER NOT NULL,
  message TEXT NOT NULL,
  created_at INTEGER NOT NULL
);

CREATE TABLE phpretro_home_ratings (
  id INTEGER PRIMARY KEY,
  profile_user_id INTEGER NOT NULL,
  rater_id INTEGER NOT NULL,
  rating INTEGER NOT NULL,
  created_at INTEGER NOT NULL
);

CREATE TABLE phpretro_minimail (
  id INTEGER PRIMARY KEY,
  sender_id INTEGER NOT NULL,
  recipient_id INTEGER NOT NULL,
  subject TEXT NOT NULL DEFAULT '',
  body TEXT NOT NULL DEFAULT '',
  conversation_id INTEGER NOT NULL DEFAULT 0,
  sent_at INTEGER NOT NULL,
  read_at INTEGER,
  deleted INTEGER NOT NULL DEFAULT 0,
  deleted_at INTEGER
);

CREATE TABLE phpretro_group_url_aliases (
  alias TEXT PRIMARY KEY,
  guild_id INTEGER NOT NULL,
  created_at INTEGER NOT NULL
);

CREATE TABLE phpretro_email_verification_tokens (
  id INTEGER PRIMARY KEY,
  user_id INTEGER NOT NULL,
  token_hash TEXT NOT NULL,
  expires_at INTEGER NOT NULL,
  used_at INTEGER
);

CREATE TABLE phpretro_client_errors (
  id INTEGER PRIMARY KEY,
  user_id INTEGER,
  ip TEXT,
  error_type TEXT,
  message TEXT,
  stack_trace TEXT,
  user_agent TEXT,
  url TEXT,
  client_version TEXT,
  created_at INTEGER NOT NULL
);

CREATE TABLE phpretro_object_reports (
  id INTEGER PRIMARY KEY,
  reporter_id INTEGER NOT NULL,
  object_type TEXT NOT NULL,
  object_id INTEGER NOT NULL DEFAULT 0,
  reason TEXT NOT NULL DEFAULT '',
  created_at INTEGER NOT NULL
);
SQL);

$hash = password_hash('password', PASSWORD_DEFAULT);
$now = time();
$look = 'hr-165-45.hd-190-1.ch-255-82.lg-285-64.sh-290-64';
$looks = [
    'hr-115-42.hd-190-1.ch-215-62.lg-285-91.sh-290-62',
    'hr-125-42.hd-180-1.ch-210-66.lg-270-64.sh-305-62',
    'hr-155-45.hd-209-1.ch-255-64.lg-275-64.sh-295-64',
    'hr-828-45.hd-180-1.ch-255-62.lg-275-62.sh-295-62',
    $look,
];

$insertUser = $pdo->prepare('INSERT INTO users (id, username, real_name, password, mail, mail_verified, account_created, account_day_of_birth, last_login, last_online, motto, look, gender, rank, credits, pixels, online) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
$insertUser->execute([1, 'karim', 'karim', $hash, 'karim@localhost', '1', $now - 86400, 1104537600, $now - 3600, $now - 3600, '', $look, 'M', 7, 500, 100, '0']);

$names = ['george', 'lisa', 'bob', 'anna', 'mike', 'sara', 'tom', 'nina', 'alex', 'kate', 'leo', 'mia', 'max', 'ivy', 'jay', 'zoe', 'rex'];
$id = 2;
foreach ($names as $i => $name) {
    $insertUser->execute([
        $id++,
        $name,
        $name,
        $hash,
        $name.'@localhost',
        '1',
        $now - (86400 * ($i + 2)),
        1104537600,
        0,
        0,
        '',
        $looks[$i % count($looks)],
        $i % 2 ? 'F' : 'M',
        1,
        0,
        0,
        '0',
    ]);
}

$pdo->prepare('INSERT INTO hotelview_news (id, title, text, button_text, button_type, button_link, image) VALUES (?,?,?,?,?,?,?)')
    ->execute([
        1,
        'Open Your Summer Calendar!',
        'Between the 1st and 31st of July, every day you will recive a free gift from your Summer Calendar. Open yours Now!',
        'Read more',
        'web',
        '/articles',
        '',
    ]);

$pdo->prepare('INSERT INTO phpretro_news (id, title, summary, story, author, categories, images, time) VALUES (?,?,?,?,?,?,?,?)')
    ->execute([
        1,
        'WOW',
        'XDXX',
        'XDDD',
        'george',
        'yes',
        '',
        strtotime('2026-09-15 12:00:00'),
    ]);

$privacy = <<<'HTML'
<h3>Privacy Policy</h3>
<p>The following discloses the information gathering and dissemination practices for this web site.</p>
<h4>Information Collection and Use</h4>
<p>We will not sell, share, or rent this information to others in ways different from what is disclosed in this statement. We collect information from our users at several different points on our website.</p>
<h4>Cookies</h4>
<p>A cookie is a piece of data stored on the user's hard drive containing information about the user. Usage of a cookie is in no way linked to any personally identifiable information while on our site. Once the user closes their browser, the cookie simply terminates. This hotel uses the <code>hotel_session</code> cookie for the website. PolarIS uses a separate hotel session cookie.</p>
<h4>Log Files</h4>
<p>We use IP addresses to analyse trends, administer the site, track user's movement, and gather broad demographic information for aggregate use. IP addresses are not linked to personally identifiable information.</p>
<h4>Newsletter</h4>
<p>If a user wishes to subscribe to our newsletter, we ask for contact information such as name and email address. This conversion does not send hotel email. Out of respect for our users' privacy, newsletter send is refused rather than faked.</p>
<h4>Surveys & Contests</h4>
<p>From time-to-time our site requests information from users via surveys or contests. Participation in these surveys or contests is completely voluntary and the user therefore has a choice whether or not to disclose this information.</p>
<h4>Security</h4>
<p>This website takes every precaution to protect our users' information. When users submit sensitive information via the website, their information is protected both online and off-line. Passwords are stored with password_hash. PolarIS users_settings is never written from this website.</p>
<h4>Notification of Changes</h4>
<p>If we decide to change our privacy policy, we will post those changes here so our users are always aware of what information we collect, how we use it, and under which circumstances, if any, we disclose it.</p>
<h4>Contact Information</h4>
<p>If you have any questions or suggestions regarding our privacy policy, please contact hotel staff.</p>
HTML;

$disclaimer = <<<'HTML'
<h3>Disclaimer</h3>
<p>By accessing and using this website, you agree to the following terms and conditions:</p>
<ul>
<li>We are not responsible for any damages that may occur to your computer as a result of visiting this website.</li>
<li>We are not responsible for the content of any linked sites.</li>
<li>We are not responsible for any content posted by users of this website.</li>
<li>We reserve the right to change these terms and conditions at any time without notice.</li>
<li>This website is not affiliated with Sulake Corporation or Habbo Hotel.</li>
</ul>
<p>HABBO is a registered trademark of Sulake Corporation. All rights reserved to their respective owner(s).</p>
HTML;

$hotelPolicy = <<<'HTML'
<ol>
<li>Keep it clean</li>
<li>Be nice</li>
<li>Don't scam</li>
<li>Don't use bots</li>
<li>Have fun</li>
</ol>
HTML;

$settings = $pdo->prepare('INSERT INTO phpretro_site_settings (setting_key, setting_value, updated_by, updated_at) VALUES (?,?,NULL,?)');
foreach ([
    'site_shortname' => 'PHPRetro',
    'site_name' => 'PHPRetro Hotel',
    'site_description' => 'A 2009 retro hotel website.',
    'site_keywords' => 'habbo, hotel, retro',
    'site_hotel_image' => 'htlview_gb.png',
    'site_webmaster_email' => 'staff@localhost',
    'site_tracking' => '',
    'site_c_images_path' => '/c_images/',
    'site_flash_promo' => '0',
    'maintenance_mode' => '0',
    'site_welcome_text' => 'Welcome to PHPRetro. Click Enter PHPRetro to open the hotel client. PolarIS remains the emulator.',
    'user_start_credits' => '0',
    'user_start_duckets' => '0',
    'paper_privacy' => $privacy,
    'paper_disclaimer' => $disclaimer,
    'paper_hotel_policy' => $hotelPolicy,
] as $key => $value) {
    $settings->execute([$key, $value, $now]);
}

$faq = $pdo->prepare('INSERT INTO phpretro_faq (id, category, question, answer, sort_order, active) VALUES (?,?,?,?,?,?)');
$faqRows = [
    [1, 'Account', 'I can\'t remember my password / email address', 'Use Forgotten password on the sign-in page. This website does not email passwords. Ask hotel staff if you still cannot get in.', 1],
    [2, 'Account', 'How do I change my password?', 'Open Account Settings from the Home tab after you sign in. Password changes write PolarIS users.password only.', 2],
    [3, 'Account', 'How do I change my email address?', 'Account email lives on PolarIS users.mail. Staff can read it from housekeeping search. This website will not fake an email change mailer.', 3],
    [4, 'Account', 'How do I delete my account?', 'Ask hotel staff. This conversion never deletes PolarIS users from the website.', 4],
    [5, 'Account', 'How do I change my look?', 'Open Account Settings. Looks and motto are the allowlisted PolarIS writes.', 5],
    [6, 'Badges', 'How do I get badges?', 'Badges are granted by PolarIS. Flash badge editors are not faked from this website.', 1],
    [7, 'Safety', 'How do I report a player?', 'Use the report tool in the hotel client, or send a helpdesk ticket from this Help page.', 1],
    [8, 'Safety', 'What is scamming?', 'Scamming is tricking other users out of furni or credits. Do not do it. Staff can ban from PolarIS, not from a fake website ban.', 2],
    [9, 'Homes', 'How do I edit My Page?', 'Open your page and click Edit. Stickers, notes, and widgets write to website-owned phpretro_homes_* tables. PolarIS credits are not charged from the store.', 1],
    [10, 'Groups', 'How do I create a group?', 'Create a Group from Home. Group create requires Club and will not insert PolarIS guilds from this website.', 1],
    [11, 'Playing', 'How do I enter the hotel?', 'Use Enter PHPRetro. The client needs NITRO_CLIENT_URL before the embed loads.', 1],
    [12, 'Shop', 'How do I buy credits?', 'This website does not sell coins. Credits stay on PolarIS.', 1],
    [13, 'Games', 'Where are the games?', 'Hotel games run inside PolarIS. This website does not fake Trax, battleball, or snowstorm wins.', 1],
    [14, 'Credits', 'What are Pixels?', 'Pixels are PolarIS activity points. The Pixels page explains them without a fake spend.', 1],
    [15, 'general', 'geez', 'Help text goes here.', 99],
];
foreach ($faqRows as $row) {
    $faq->execute([$row[0], $row[1], $row[2], $row[3], $row[4], 1]);
}

$layout = $pdo->prepare('INSERT INTO phpretro_myhabbo_layouts (user_id, guild_id, column_number, widget_key, position, visible, privacy) VALUES (1,0,?,?,?,1,\'public\')');
foreach ([
    [1, 'profilewidget', 0],
    [1, 'guestbookwidget', 5],
    [1, 'ratingwidget', 10],
    [2, 'friendswidget', 0],
    [2, 'groupswidget', 4],
    [2, 'roomswidget', 8],
] as $row) {
    $layout->execute($row);
}

$cat = $pdo->prepare('INSERT INTO phpretro_homes_catalogue (id, name, description, type, data, price, amount, category, category_id, min_rank, placement) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
$cat->execute([1, 'Skull', 'A skull sticker', 'sticker', 'skull', 0, 1, 'Default', 1, 1, 'anywhere']);
$cat->execute([2, 'Flower', 'A flower sticker', 'sticker', 'flower1png', 0, 1, 'Default', 1, 1, 'anywhere']);
$cat->execute([3, 'Yellow flower', 'A yellow flower', 'sticker', 'flower2', 0, 1, 'Default', 1, 1, 'anywhere']);
$cat->execute([4, 'Notes', 'Stickie notes', 'note', 'stickienote', 0, 5, 'Notes', 101, 1, 'anywhere']);
$cat->execute([5, 'Wood Background', 'Wooden background', 'background', 'bg_pattern_abstract2', 0, 1, 'Backgrounds', 104, 1, 'anywhere']);
$cat->execute([101, 'Profile Widget', 'Your profile.', 'widget', 'profilewidget', 0, 1, 'Widgets', 100, 1, 'homes']);
$cat->execute([102, 'Guestbook Widget', 'Comments on your page.', 'widget', 'guestbookwidget', 0, 1, 'Widgets', 100, 1, 'homes']);
$cat->execute([103, 'High Scores Widget', 'High scores.', 'widget', 'highscoreswidget', 0, 1, 'Widgets', 100, 1, 'homes']);
$cat->execute([104, 'Badges Widget', 'Your badges.', 'widget', 'badgeswidget', 0, 1, 'Widgets', 100, 1, 'homes']);
$cat->execute([105, 'Friends Widget', 'Your friends.', 'widget', 'friendswidget', 0, 1, 'Widgets', 100, 1, 'homes']);
$cat->execute([106, 'Groups Widget', 'Your groups.', 'widget', 'groupswidget', 0, 1, 'Widgets', 100, 1, 'homes']);
$cat->execute([107, 'Rooms Widget', 'Your rooms.', 'widget', 'roomswidget', 0, 1, 'Widgets', 100, 1, 'homes']);
$cat->execute([109, 'Rating Widget', 'Allows others to vote on your page.', 'widget', 'ratingwidget', 0, 1, 'Widgets', 100, 1, 'homes']);
$cat->execute([110, 'Group Info Widget', 'Group information.', 'widget', 'groupinfowidget', 0, 1, 'Widgets', 100, 1, 'groups']);
$cat->execute([111, 'Group Guestbook', 'Comments on the group page.', 'widget', 'guestbookwidget', 0, 1, 'Widgets', 100, 1, 'groups']);
$cat->execute([112, 'Members Widget', 'Members of this group.', 'widget', 'memberwidget', 0, 1, 'Widgets', 100, 1, 'groups']);
$item = $pdo->prepare('INSERT INTO phpretro_homes_items (user_id, guild_id, catalogue_id, item_type, skin, data, x, y, z, placed) VALUES (?,?,?,?,?,?,?,?,?,?)');
$item->execute([1, 0, 1, 'sticker', '', '', 40, 40, 2, 1]);
$item->execute([1, 0, 2, 'sticker', '', '', 520, 80, 3, 1]);
$item->execute([1, 0, 3, 'sticker', '', '', 580, 180, 4, 1]);
$item->execute([1, 0, 1, 'sticker', '', '', 0, 0, 0, 0]);
$item->execute([1, 0, 2, 'sticker', '', '', 0, 0, 0, 0]);
$item->execute([1, 0, 4, 'stickie', '', '', 0, 0, 0, 0]);
$item->execute([1, 0, 4, 'stickie', '', '', 0, 0, 0, 0]);
$item->execute([1, 0, 5, 'background', '', '', 0, 0, 0, 0]);

$pdo->prepare('INSERT INTO rooms (id, name, owner_id, owner_name, users, users_max, is_public, is_staff_picked, description) VALUES (?,?,?,?,?,?,?,?,?)')
    ->execute([1, 'Welcome Lounge', 1, 'karim', 0, 25, '1', '1', 'Public welcome room']);
$pdo->prepare('INSERT INTO catalog_items (id, catalog_name) VALUES (?,?)')->execute([13, 'Throne']);
$pdo->prepare('INSERT INTO phpretro_user_reports (id, reporter_id, reported_user_id, reason, evidence, status, created_at) VALUES (?,?,?,?,?,?,?)')
    ->execute([1, 2, 3, 'scamming', 'Tried to swap a throne for a chair', 'open', $now - 3600]);

$pdo->prepare('INSERT INTO guilds (id, user_id, name, description, room_id, state, badge, date_created, forum, read_forum, post_messages) VALUES (?,?,?,?,?,?,?,?,?,?,?)')
    ->execute([1, 1, 'PHPRetro Staff', 'Official staff group. PolarIS owns guilds; this website only reads them.', 1, 0, '', $now - 86400, 1, 'EVERYONE', 'MEMBERS']);
$member = $pdo->prepare('INSERT INTO guilds_members (guild_id, user_id, level_id) VALUES (?,?,?)');
$member->execute([1, 1, 0]);
$member->execute([1, 2, 2]);
$pdo->prepare('INSERT INTO guilds_forums_threads (id, guild_id, opener_id, subject, posts_count, created_at, updated_at, state, pinned, locked) VALUES (?,?,?,?,?,?,?,?,?,?)')
    ->execute([1, 1, 1, 'Welcome to the staff group', 2, $now - 7200, $now - 3600, 0, 1, 0]);
$post = $pdo->prepare('INSERT INTO guilds_forums_comments (thread_id, user_id, message, created_at) VALUES (?,?,?,?)');
$post->execute([1, 1, 'This forum is PolarIS guilds_forums_threads. The website shows it read-only.', $now - 7200]);
$post->execute([1, 2, 'Got it.', $now - 3600]);
$pdo->prepare('INSERT INTO messenger_friendships (user_one_id, user_two_id) VALUES (?,?)')->execute([1, 2]);
$pdo->prepare('INSERT INTO messenger_friendships (user_one_id, user_two_id) VALUES (?,?)')->execute([2, 1]);
$pdo->prepare('INSERT INTO vouchers (id, code, credits, points, catalog_item_id, amount, "limit") VALUES (?,?,?,?,?,?,?)')
    ->execute([1, 'WELCOME', 10, 0, 0, 1, -1]);
$pdo->prepare('INSERT INTO phpretro_transactions (user_id, type, amount, balance_after, description, reference_id, created_at) VALUES (?,?,?,?,?,?,?)')
    ->execute([1, 'seed', 500, 500, 'Preview purse', 'seed', $now - 86400]);
$pdo->prepare('INSERT INTO phpretro_admin_action_log (admin_id, action_type, target_type, target_id, details, ip, created_at) VALUES (?,?,?,?,?,?,?)')
    ->execute([1, 'seed', 'site', null, 'Preview holodb seeded', '127.0.0.1', $now]);

$gLayout = $pdo->prepare('INSERT INTO phpretro_myhabbo_layouts (user_id, guild_id, column_number, widget_key, position, visible, privacy) VALUES (1,1,?,?,?,1,\'public\')');
foreach ([
    [1, 'groupinfowidget', 0],
    [1, 'guestbookwidget', 5],
    [2, 'memberwidget', 0],
] as $row) {
    $gLayout->execute($row);
}
$pdo->prepare('INSERT INTO phpretro_myhabbo_guestbook (profile_user_id, author_user_id, message, created_at) VALUES (?,?,?,?)')
    ->execute([1, 2, 'Nice page!', $now - 3600]);
$pdo->prepare('INSERT INTO phpretro_group_guestbook (guild_id, author_user_id, message, created_at) VALUES (?,?,?,?)')
    ->execute([1, 2, 'Staff group guestbook is website-owned.', $now - 1800]);
$pdo->prepare('INSERT INTO phpretro_minimail (sender_id, recipient_id, subject, body, conversation_id, sent_at, read_at, deleted) VALUES (?,?,?,?,?,?,?,?)')
    ->execute([2, 1, 'Welcome', 'Minimail lives in phpretro_minimail, not PolarIS messenger.', 0, $now - 600, null, 0]);
$pdo->prepare('INSERT INTO phpretro_group_url_aliases (alias, guild_id, created_at) VALUES (?,?,?)')
    ->execute(['staff', 1, $now]);
$pdo->prepare('INSERT INTO phpretro_home_ratings (profile_user_id, rater_id, rating, created_at) VALUES (?,?,?,?)')
    ->execute([1, 2, 5, $now - 1200]);

echo "holodb sqlite seeded at {$path}\n";
