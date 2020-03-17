<?php
define('CONFIG_FILE', __DIR__ . '/config.ini');
define('DATABASE_FILE', __DIR__ . '/channels.json');

require_once __DIR__ . '/libraries/TeamSpeak3/TeamSpeak3.php';
require_once __DIR__ . '/php/Config.php';
require_once __DIR__ . '/php/Database.php';

if (!file_exists(CONFIG_FILE) || filesize(CONFIG_FILE) < 1) {
    msg('Failed to load config file.');
    exit(1);
}

msg('Bot Started | Channel Creator');
msg('PHP ' . PHP_VERSION . ' | TS3Lib ' . TeamSpeak3::LIB_VERSION);
msg('Bot by Ondra3211 | https://github.com/Ondra3211' . PHP_EOL);

try
{
    TeamSpeak3::init();

    TeamSpeak3_Helper_Signal::getInstance()->subscribe('notifyClientmoved', 'onMove');
    TeamSpeak3_Helper_Signal::getInstance()->subscribe('serverqueryWaitTimeout', 'onTimeout');
    TeamSpeak3_Helper_Signal::getInstance()->subscribe('notifyChanneldeleted', 'onDelete');

    $cf = new Config;

    $host = $cf->get('BOT', 'host');
    $qport = $cf->get('BOT', 'qport');
    $vport = $cf->get('BOT', 'vport');
    $user = rawurlencode($cf->get('BOT', 'username'));
    $pass = rawurlencode($cf->get('BOT', 'password'));
    $nick = rawurlencode($cf->get('BOT', 'nickname'));

    $uri = "serverquery://$user:$pass@$host:$qport/?server_port=$vport&nickname=$nick&timeout=3&blocking=0";

    $ts3 = TeamSpeak3::factory($uri);
    $ts3->notifyRegister('channel');

    msg('Connected to ' . $ts3->getProperty('virtualserver_name'));

    try
    {
        $ts3->clientGetById($ts3->whoamiGet('client_id'))->move((int)$cf->get('BOT', 'default_channel'));
    } catch (TeamSpeak3_Exception $e) {
        msg('Failed to join default channel: ' . $e->getMessage());
    }

    $db = new Database;

    while (true) {
        $ts3->getAdapter()->wait();
    }

} catch (TeamSpeak3_Exception $e) {
    msg($e->getCode() . ': ' . $e->getMessage());
    exit(1);
}

function onMove(TeamSpeak3_Adapter_ServerQuery_Event $event, TeamSpeak3_Node_Host $host)
{
    global $cf, $db;

    $client = new TeamSpeak3_Node_Client($host->serverGetSelected(), ['clid' => $event['clid']]);

    if ($client->getProperty('client_type') === 1) {
        return;
    }

    $uid = (string)$client->getProperty('client_unique_identifier');

    if ($event['ctid'] == $cf->get('SETTINGS', 'create_channel')) {

        if ($db->hasChannel($uid)) {
            $client->kick(TeamSpeak3::KICK_CHANNEL, $cf->get('MESSAGES', 'kick_create'));
        } else {
            $name = str_replace('[NICKNAME]', $client->getProperty('client_nickname'), $cf->get('MESSAGES', 'channel_name'));
            $password = mt_rand(0, 9999);

            // zkusit 10x přejmenovat místnost
            for ($i = 0; $i < 10; $i++) {
                try
                {
                    $cid = $host->serverGetSelected()->channelCreate([
                        'channel_name' => $name,
                        'channel_topic' => '',
                        'channel_password' => $password,
                        'channel_flag_permanent' => true,
                        'cpid' => $cf->get('SETTINGS', 'main_channel'),
                    ]);
                    break;
                } catch (TeamSpeak3_Exception $e) {
                    if ($e->getCode() === 768) {
                        msg('Channel with ID: ' . $cf->get('SETTINGS', 'main_channel') . ' doesn\'t exists. Cannot create channel');
                        exit(1);
                    } elseif ($e->getCode() === 771) {
                        if (mb_strlen($name) >= 39) {
                            $name = mt_rand(0, 9) . mb_substr($name, 0, -1);
                        } else {
                            $name .= mt_rand(0, 9);
                        }
                    } elseif ($e->getCode() === 1541) {
                        $name = mb_substr($name, 0, (40 - mb_strlen($name)));
                    }
                }
            }

            $client->setChannelGroup($cid, $cf->get('SETTINGS', 'channel_admin'));
            $client->move($cid);
            $client->message(str_replace('[PASSWORD]', $password, $cf->get('MESSAGES', 'create_channel')));

            $db->writeChannel($cid, $uid);

        }
    } elseif ($event['ctid'] == $cf->get('SETTINGS', 'move_channel')) {

        if (!$db->hasChannel($uid)) {
            $client->kick(TeamSpeak3::KICK_CHANNEL, $cf->get('MESSAGES', 'kick_move'));
        } else {
            $client->move($db->getChannel($uid));
        }
    }
}

function onDelete(TeamSpeak3_Adapter_ServerQuery_Event $event)
{
    global $db;
    $db->deleteChannel($event['cid']);
}

function onTimeout($seconds, TeamSpeak3_Adapter_ServerQuery $adapter)
{
    if ($adapter->getQueryLastTimestamp() < (time() - 250)) {
        $adapter->request('clientupdate');
    }
}

function msg($msg = '')
{
    echo '[' . date('d.m.Y H:i:s') . '] ' . (string)$msg . PHP_EOL;
}
