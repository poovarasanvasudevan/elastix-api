<?php

/*
All Params:
deny, secret, dtmfmode, canreinvite, context, host, trustrpid, sendrpid, type, nat, port, qualify, qualifyfreq, transport, avpf, icesupport, encryption, callgroup, pickupgroup, dial, mailbox, permit, callerid, callcounter, faxdetect
Additional Params:
accountcode, account
*/

class Extension
{
    public function __construct($dict, $flag)
    {
        if ($flag === "insert" || $flag == "update") {
            $this->name = $dict["name"];
            $this->deny = $dict["deny"];
            $this->secret = $dict["secret"];
            $this->dtmfmode = $dict["dtmfmode"];
            $this->canreinvite = $dict["canreinvite"];
            $this->context = $dict["context"];
            $this->host = $dict["host"];
            $this->trustrpid = $dict["trustrpid"];
            $this->sendrpid = $dict["sendrpid"];
            $this->type = $dict["type"];
            $this->nat = $dict["nat"];
            $this->port = $dict["port"];
            $this->qualify = $dict["qualify"];
            $this->qualifyfreq = $dict["qualifyfreq"];
            $this->transport = $dict["transport"];
            $this->avpf = $dict["avpf"];
            $this->icesupport = $dict["icesupport"];
            $this->encryption = $dict["encryption"];
            $this->callgroup = $dict["callgroup"];
            $this->pickupgroup = $dict["pickupgroup"];
            $this->dial = $dict["dial"];
            $this->mailbox = $dict["mailbox"];
            $this->permit = $dict["permit"];
            $this->callerid = $dict["callerid"];
            $this->callcounter = $dict["callcounter"];
            $this->faxdetect = $dict["faxdetect"];
            $this->accountcode = "";
            $this->account = $dict["account"];
        } else if ($flag === "delete") {
            $this->account = $dict["account"];
        } else if ($flag === "pjsip_insert" || $flag == "pjsip_update") {
            $this->account = $dict["account"];
            $this->fingerprint = $dict["fingerprint"];
            $this->accountcode = $dict["accountcode"];
            $this->aggregate_mwi = $dict["aggregate_mwi"];
            $this->allow = $dict["allow"];
            $this->avpf = $dict["avpf"];
            $this->bundle = $dict["bundle"];
            $this->callerid = $dict["callerid"];
            $this->context = $dict["context"];
            $this->defaultuser = $dict["defaultuser"];
            $this->device_state_busy_at = $dict["device_state_busy_at"];
            $this->dial = $dict["dial"];
            $this->direct_media = $dict["direct_media"];
            $this->disallow = $dict["disallow"];
            $this->dtmfmode = $dict["dtmfmode"];
            $this->force_rport = $dict["force_rport"];
            $this->icesupport = $dict["icesupport"];
            $this->match = $dict["match"];
            $this->max_audio_streams = $dict["max_audio_streams"];
            $this->max_contacts = $dict["max_contacts"];
            $this->max_video_streams = $dict["max_video_streams"];
            $this->maximum_expiration = $dict["maximum_expiration"];
            $this->media_encryption = $dict["media_encryption"];
            $this->media_encryption_optimistic = $dict["media_encryption_optimistic"];
            $this->media_use_received_transport = $dict["media_use_received_transport"];
            $this->message_context = $dict["message_context"];
            $this->minimum_expiration = $dict["minimum_expiration"];
            $this->mwi_subscription = $dict["mwi_subscription"];
            $this->namedcallgroup = $dict["namedcallgroup"];
            $this->namedpickupgroup = $dict["namedpickupgroup"];
            $this->outbound_auth = $dict["outbound_auth"];
            $this->outbound_proxy = $dict["outbound_proxy"];
            $this->qualifyfreq = $dict["qualifyfreq"];
            $this->refer_blind_progress = $dict["refer_blind_progress"];
            $this->remove_existing = $dict["remove_existing"];
            $this->rewrite_contact = $dict["rewrite_contact"];
            $this->rtcp_mux = $dict["rtcp_mux"];
            $this->rtp_symmetric = $dict["rtp_symmetric"];
            $this->rtp_timeout = $dict["rtp_timeout"];
            $this->rtp_timeout_hold = $dict["rtp_timeout_hold"];
            $this->secret = $dict["secret"];
            $this->secret_origional = $dict["secret_origional"];
            $this->send_connected_line = $dict["send_connected_line"];
            $this->sendrpid = $dict["sendrpid"];
            $this->sipdriver = $dict["sipdriver"];
            $this->timers = $dict["timers"];
            $this->timers_min_se = $dict["timers_min_se"];
            $this->transport = $dict["transport"];
            $this->trustrpid = $dict["trustrpid"];
            $this->user_eq_phone = $dict["user_eq_phone"];
            $this->name = $dict["name"];
        }
    }

    public function select_sip_sqlscript()
    {
        $sql_script = "SELECT * FROM sip WHERE id = '" . $this->account . "'";

        return $sql_script;
    }

    public function insert_into_pjsip_sqlscript()
    {
        $sql_script = "INSERT IGNORE INTO sip (id, keyword, data, flags) VALUES " .
            "('" . $this->account . "', 'secret', '" . $this->secret . "', 2)," .
            "('" . $this->account . "', 'dtmfmode', '" . $this->dtmfmode . "', 3)," .
            "('" . $this->account . "', 'context', '" . $this->context . "', 4)," .
            "('" . $this->account . "', 'defaultuser', '" . $this->defaultuser . "', 5)," .
            "('" . $this->account . "', 'trustrpid', '" . $this->trustrpid . "', 6)," .
            "('" . $this->account . "', 'send_connected_line', '" . $this->send_connected_line . "', 7)," .
            "('" . $this->account . "', 'user_eq_phone', '" . $this->user_eq_phone . "', 8)," .
            "('" . $this->account . "', 'sendrpid', '" . $this->sendrpid . "', 9)," .
            "('" . $this->account . "', 'qualifyfreq', '" . $this->qualifyfreq . "', 10)," .
            "('" . $this->account . "', 'transport', '" . $this->transport . "', 11)," .
            "('" . $this->account . "', 'avpf', '" . $this->avpf . "', 12)," .
            "('" . $this->account . "', 'icesupport', '" . $this->icesupport . "', 13)," .
            "('" . $this->account . "', 'rtcp_mux', '" . $this->rtcp_mux . "', 14)," .
            "('" . $this->account . "', 'namedcallgroup', '" . $this->namedcallgroup . "', 15)," .
            "('" . $this->account . "', 'namedpickupgroup', '" . $this->namedpickupgroup . "', 16)," .
            "('" . $this->account . "', 'disallow', '" . $this->disallow . "', 17)," .
            "('" . $this->account . "', 'allow', '" . $this->allow . "', 18)," .
            "('" . $this->account . "', 'dial', '" . $this->dial . "', 19)," .
            "('" . $this->account . "', 'accountcode', '" . $this->accountcode . "', 20)," .
            "('" . $this->account . "', 'max_contacts', '" . $this->max_contacts . "', 21)," .
            "('" . $this->account . "', 'remove_existing', '" . $this->remove_existing . "', 22)," .
            "('" . $this->account . "', 'media_use_received_transport', '" . $this->media_use_received_transport . "', 23)," .
            "('" . $this->account . "', 'rtp_symmetric', '" . $this->rtp_symmetric . "', 24)," .
            "('" . $this->account . "', 'rewrite_contact', '" . $this->rewrite_contact . "', 25)," .
            "('" . $this->account . "', 'force_rport', '" . $this->force_rport . "', 26)," .
            "('" . $this->account . "', 'mwi_subscription', '" . $this->mwi_subscription . "', 27)," .
            "('" . $this->account . "', 'aggregate_mwi', '" . $this->aggregate_mwi . "', 28)," .
            "('" . $this->account . "', 'bundle', '" . $this->bundle . "', 29)," .
            "('" . $this->account . "', 'max_audio_streams', '" . $this->max_audio_streams . "', 30)," .
            "('" . $this->account . "', 'max_video_streams', '" . $this->max_video_streams . "', 31)," .
            "('" . $this->account . "', 'media_encryption', '" . $this->media_encryption . "', 32)," .
            "('" . $this->account . "', 'timers', '" . $this->timers . "', 33)," .
            "('" . $this->account . "', 'timers_min_se', '" . $this->timers_min_se . "', 34)," .
            "('" . $this->account . "', 'direct_media', '" . $this->direct_media . "', 35)," .
            "('" . $this->account . "', 'media_encryption_optimistic', '" . $this->media_encryption_optimistic . "', 36)," .
            "('" . $this->account . "', 'refer_blind_progress', '" . $this->refer_blind_progress . "', 37)," .
            "('" . $this->account . "', 'device_state_busy_at', '" . $this->device_state_busy_at . "', 38)," .
            "('" . $this->account . "', 'match', '" . $this->match . "', 39)," .
            "('" . $this->account . "', 'maximum_expiration', '" . $this->maximum_expiration . "', 40)," .
            "('" . $this->account . "', 'minimum_expiration', '" . $this->minimum_expiration . "', 41)," .
            "('" . $this->account . "', 'rtp_timeout', '" . $this->rtp_timeout . "', 42)," .
            "('" . $this->account . "', 'rtp_timeout_hold', '" . $this->rtp_timeout_hold . "', 43)," .
            "('" . $this->account . "', 'outbound_proxy', '" . $this->outbound_proxy . "', 44)," .
            "('" . $this->account . "', 'message_context', '" . $this->message_context . "', 46)," .
            "('" . $this->account . "', 'secret_origional', '" . $this->secret . "', 47)," .
            "('" . $this->account . "', 'sipdriver', '" . $this->sipdriver . "', 48)," .
            "('" . $this->account . "', 'account', '" . $this->account . "', 49)," .
            "('" . $this->account . "', 'callerid', '" . $this->callerid . "', 50)";
        return $sql_script;
    }

    public function update_pjsip_sqlscript()
    {
        $sql_script = "INSERT IGNORE INTO sip (id, keyword, data, flags) VALUES " .
            "('" . $this->account . "', 'secret', '" . $this->secret . "', 2)," .
            "('" . $this->account . "', 'dtmfmode', '" . $this->dtmfmode . "', 3)," .
            "('" . $this->account . "', 'context', '" . $this->context . "', 4)," .
            "('" . $this->account . "', 'defaultuser', '" . $this->defaultuser . "', 5)," .
            "('" . $this->account . "', 'trustrpid', '" . $this->trustrpid . "', 6)," .
            "('" . $this->account . "', 'send_connected_line', '" . $this->send_connected_line . "', 7)," .
            "('" . $this->account . "', 'user_eq_phone', '" . $this->user_eq_phone . "', 8)," .
            "('" . $this->account . "', 'sendrpid', '" . $this->sendrpid . "', 9)," .
            "('" . $this->account . "', 'qualifyfreq', '" . $this->qualifyfreq . "', 10)," .
            "('" . $this->account . "', 'transport', '" . $this->transport . "', 11)," .
            "('" . $this->account . "', 'avpf', '" . $this->avpf . "', 12)," .
            "('" . $this->account . "', 'icesupport', '" . $this->icesupport . "', 13)," .
            "('" . $this->account . "', 'rtcp_mux', '" . $this->rtcp_mux . "', 14)," .
            "('" . $this->account . "', 'namedcallgroup', '" . $this->namedcallgroup . "', 15)," .
            "('" . $this->account . "', 'namedpickupgroup', '" . $this->namedpickupgroup . "', 16)," .
            "('" . $this->account . "', 'disallow', '" . $this->disallow . "', 17)," .
            "('" . $this->account . "', 'allow', '" . $this->allow . "', 18)," .
            "('" . $this->account . "', 'dial', '" . $this->dial . "', 19)," .
            "('" . $this->account . "', 'accountcode', '" . $this->accountcode . "', 20)," .
            "('" . $this->account . "', 'max_contacts', '" . $this->max_contacts . "', 21)," .
            "('" . $this->account . "', 'remove_existing', '" . $this->remove_existing . "', 22)," .
            "('" . $this->account . "', 'media_use_received_transport', '" . $this->media_use_received_transport . "', 23)," .
            "('" . $this->account . "', 'rtp_symmetric', '" . $this->rtp_symmetric . "', 24)," .
            "('" . $this->account . "', 'rewrite_contact', '" . $this->rewrite_contact . "', 25)," .
            "('" . $this->account . "', 'force_rport', '" . $this->force_rport . "', 26)," .
            "('" . $this->account . "', 'mwi_subscription', '" . $this->mwi_subscription . "', 27)," .
            "('" . $this->account . "', 'aggregate_mwi', '" . $this->aggregate_mwi . "', 28)," .
            "('" . $this->account . "', 'bundle', '" . $this->bundle . "', 29)," .
            "('" . $this->account . "', 'max_audio_streams', '" . $this->max_audio_streams . "', 30)," .
            "('" . $this->account . "', 'max_video_streams', '" . $this->max_video_streams . "', 31)," .
            "('" . $this->account . "', 'media_encryption', '" . $this->media_encryption . "', 32)," .
            "('" . $this->account . "', 'timers', '" . $this->timers . "', 33)," .
            "('" . $this->account . "', 'timers_min_se', '" . $this->timers_min_se . "', 34)," .
            "('" . $this->account . "', 'direct_media', '" . $this->direct_media . "', 35)," .
            "('" . $this->account . "', 'media_encryption_optimistic', '" . $this->media_encryption_optimistic . "', 36)," .
            "('" . $this->account . "', 'refer_blind_progress', '" . $this->refer_blind_progress . "', 37)," .
            "('" . $this->account . "', 'device_state_busy_at', '" . $this->device_state_busy_at . "', 38)," .
            "('" . $this->account . "', 'match', '" . $this->match . "', 39)," .
            "('" . $this->account . "', 'maximum_expiration', '" . $this->maximum_expiration . "', 40)," .
            "('" . $this->account . "', 'minimum_expiration', '" . $this->minimum_expiration . "', 41)," .
            "('" . $this->account . "', 'rtp_timeout', '" . $this->rtp_timeout . "', 42)," .
            "('" . $this->account . "', 'rtp_timeout_hold', '" . $this->rtp_timeout_hold . "', 43)," .
            "('" . $this->account . "', 'outbound_proxy', '" . $this->outbound_proxy . "', 44)," .
            "('" . $this->account . "', 'message_context', '" . $this->message_context . "', 46)," .
            "('" . $this->account . "', 'secret_origional', '" . $this->secret . "', 47)," .
            "('" . $this->account . "', 'sipdriver', '" . $this->sipdriver . "', 48)," .
            "('" . $this->account . "', 'account', '" . $this->account . "', 49)," .
            "('" . $this->account . "', 'callerid', '" . $this->callerid . "', 50)" .
            " ON DUPLICATE KEY UPDATE id=VALUES(id), keyword=VALUES(keyword) , data=VALUES(data), flags=VALUES(flags)";
        return $sql_script;
    }

    public function insert_pjsip_cert() {
        $sql_script = "INSERT INTO certman_mapping(id,cid,verify,actpass,rekey) VALUES('" . $this->account . "','" . $this->fingerprint . "','fingerprint','actpass','0') ON DUPLICATE KEY UPDATE id=VALUES(id), cid=VALUES(cid), verify=VALUES(verify), actpass=VALUES(actpass), rekey=VALUES(rekey)";
        return $sql_script;
    }

    public function insert_into_sip_sqlscript()
    {
        $sql_script = "INSERT IGNORE INTO sip (id, keyword, data, flags) VALUES " .
            "('" . $this->account . "', 'deny', '" . $this->deny . "', 2)," .
            "('" . $this->account . "', 'secret', '" . $this->secret . "', 3)," .
            "('" . $this->account . "', 'dtmfmode', '" . $this->dtmfmode . "', 4)," .
            "('" . $this->account . "', 'canreinvite', '" . $this->canreinvite . "', 5)," .
            "('" . $this->account . "', 'context', '" . $this->context . "', 6)," .
            "('" . $this->account . "', 'host', '" . $this->host . "', 7)," .
            "('" . $this->account . "', 'trustrpid', '" . $this->trustrpid . "', 8)," .
            "('" . $this->account . "', 'sendrpid', '" . $this->sendrpid . "', 9)," .
            "('" . $this->account . "', 'type', '" . $this->type . "', 10)," .
            "('" . $this->account . "', 'nat', '" . $this->nat . "', 11)," .
            "('" . $this->account . "', 'port', '" . $this->port . "', 12)," .
            "('" . $this->account . "', 'qualify', '" . $this->qualify . "', 13)," .
            "('" . $this->account . "', 'qualifyfreq', '" . $this->qualifyfreq . "', 14)," .
            "('" . $this->account . "', 'transport', '" . $this->transport . "', 15)," .
            "('" . $this->account . "', 'avpf', '" . $this->avpf . "', 16)," .
            "('" . $this->account . "', 'icesupport', '" . $this->icesupport . "', 17)," .
            "('" . $this->account . "', 'encryption', '" . $this->encryption . "', 18)," .
            "('" . $this->account . "', 'callgroup', '" . $this->callgroup . "', 19)," .
            "('" . $this->account . "', 'pickupgroup', '" . $this->pickupgroup . "', 20)," .
            "('" . $this->account . "', 'dial', '" . $this->dial . "', 21)," .
            "('" . $this->account . "', 'mailbox', '" . $this->mailbox . "', 22)," .
            "('" . $this->account . "', 'permit', '" . $this->permit . "', 23)," .
            "('" . $this->account . "', 'callerid', '" . $this->callerid . "', 24)," .
            "('" . $this->account . "', 'callcounter', '" . $this->callcounter . "', 25)," .
            "('" . $this->account . "', 'faxdetect', '" . $this->faxdetect . "', 26)," .
            "('" . $this->account . "', 'accountcode', '" . $this->accountcode . "', 27)," .
            "('" . $this->account . "', 'account', '" . $this->account . "', 28)";
        return $sql_script;
    }

    public function update_sip_sqlscript()
    {
        $sql_script = "INSERT INTO sip (id, keyword, data, flags) VALUES " .
            "('" . $this->account . "', 'deny', '" . $this->deny . "', 2)," .
            "('" . $this->account . "', 'secret', '" . $this->secret . "', 3)," .
            "('" . $this->account . "', 'dtmfmode', '" . $this->dtmfmode . "', 4)," .
            "('" . $this->account . "', 'canreinvite', '" . $this->canreinvite . "', 5)," .
            "('" . $this->account . "', 'context', '" . $this->context . "', 6)," .
            "('" . $this->account . "', 'host', '" . $this->host . "', 7)," .
            "('" . $this->account . "', 'trustrpid', '" . $this->trustrpid . "', 8)," .
            "('" . $this->account . "', 'sendrpid', '" . $this->sendrpid . "', 9)," .
            "('" . $this->account . "', 'type', '" . $this->type . "', 10)," .
            "('" . $this->account . "', 'nat', '" . $this->nat . "', 11)," .
            "('" . $this->account . "', 'port', '" . $this->port . "', 12)," .
            "('" . $this->account . "', 'qualify', '" . $this->qualify . "', 13)," .
            "('" . $this->account . "', 'qualifyfreq', '" . $this->qualifyfreq . "', 14)," .
            "('" . $this->account . "', 'transport', '" . $this->transport . "', 15)," .
            "('" . $this->account . "', 'avpf', '" . $this->avpf . "', 16)," .
            "('" . $this->account . "', 'icesupport', '" . $this->icesupport . "', 17)," .
            "('" . $this->account . "', 'encryption', '" . $this->encryption . "', 18)," .
            "('" . $this->account . "', 'callgroup', '" . $this->callgroup . "', 19)," .
            "('" . $this->account . "', 'pickupgroup', '" . $this->pickupgroup . "', 20)," .
            "('" . $this->account . "', 'dial', '" . $this->dial . "', 21)," .
            "('" . $this->account . "', 'mailbox', '" . $this->mailbox . "', 22)," .
            "('" . $this->account . "', 'permit', '" . $this->permit . "', 23)," .
            "('" . $this->account . "', 'callerid', '" . $this->callerid . "', 24)," .
            "('" . $this->account . "', 'callcounter', '" . $this->callcounter . "', 25)," .
            "('" . $this->account . "', 'faxdetect', '" . $this->faxdetect . "', 26)," .
            "('" . $this->account . "', 'accountcode', '" . $this->accountcode . "', 27)," .
            "('" . $this->account . "', 'account', '" . $this->account . "', 28)" .
            " ON DUPLICATE KEY UPDATE id=VALUES(id), keyword=VALUES(keyword) , data=VALUES(data), flags=VALUES(flags)";
        //
        return $sql_script;
    }

    public function delete_sip_sqlscript()
    {
        $sql_script = "DELETE FROM sip WHERE id='" . $this->account . "'";
        return $sql_script;
    }

    /*
        extension, password, name, voicemail, ringtimer, noanswer, recording, outboundcid, sipname, mohclass, noanswer_cid, busy_cid, chanunavail_cid, noanswer_dest, busy_dest, chanunavail_dest
    */
    public function insert_into_users_sqlscript()
    {
        $sql_script = "INSERT IGNORE INTO users (extension, password, name, voicemail, ringtimer, noanswer, recording, outboundcid, sipname, mohclass, noanswer_cid, busy_cid, chanunavail_cid, noanswer_dest, busy_dest, chanunavail_dest) VALUES " .
            "('" . $this->account . "', '', '" . $this->name . "', 'novm', 0, '', '', '', '" . $this->account . "', 'default', '', '', '', '', '', '')";
        return $sql_script;
    }

    public function insert_into_pjsip_users_sqlscript()
    {
        $sql_script = "INSERT IGNORE INTO users (extension, password, name, voicemail, ringtimer, noanswer, recording, outboundcid, sipname, mohclass, noanswer_cid, busy_cid, chanunavail_cid, noanswer_dest, busy_dest, chanunavail_dest) VALUES " .
            "('" . $this->account . "', '', '" . $this->name . "', 'novm', 0, '', '', '', '', 'default', '', '', '', '', '', '')";
        return $sql_script;
    }

    public function update_users_sqlscript()
    {
        $sql_script = "UPDATE users SET extension='" . $this->account . "', name='" . $this->name . "', sipname='" . $this->account . "' WHERE extension = '" . $this->account . "'";
        return $sql_script;
    }

    public function update_pjsip_users_sqlscript()
    {
        $sql_script = "UPDATE users SET extension='" . $this->account . "', name='" . $this->name . "' WHERE extension = '" . $this->account . "'";
        return $sql_script;
    }

    public function delete_users_sqlscript()
    {
        $sql_script = "DELETE FROM users WHERE extension = '" . $this->account . "'";
        return $sql_script;
    }

    public function insert_into_devices_sqlscript()
    {
        $sql_script = "INSERT IGNORE INTO devices (id, tech, dial, devicetype, user, description, emergency_cid) VALUES ('" . $this->account . "', 'sip', '" . $this->dial . "', 'fixed', '" . $this->account . "', '" . $this->account . "', '')";
        return $sql_script;
    }

    public function insert_into_pjsip_devices_sqlscript()
    {
        $sql_script = "INSERT IGNORE INTO devices (id, tech, dial, devicetype, user, description, emergency_cid) VALUES ('" . $this->account . "', 'pjsip', '" . $this->dial . "', 'fixed', '" . $this->account . "', '" . $this->account . "', '')";
        return $sql_script;
    }

    public function delete_devices_sqlscript()
    {
        $sql_script = "DELETE FROM devices WHERE id = '" . $this->account . "'";
        return $sql_script;
    }
}

class FindMeFollow
{
    /*
        grpnum, strategy, grptime, grppre, grplist, annmsg_id, postdest, dring, remotealert_id, needsconf, toolate_id, pre_ring, ringing

        http://community.freepbx.org/t/sql-database-clears-information-when-open-freepbx-webpage/15000/5
    */
    function __construct($dict, $flag)
    {
        if ($flag === "insert" || $flag == "update") {
            $this->grpnum = $dict["grpnum"];
            $this->strategy = $dict["strategy"];
            $this->grptime = $dict["grptime"];
            $this->grppre = $dict["grppre"];
            $this->grplist = $dict["grplist"];
            $this->annmsg_id = $dict["annmsg_id"];
            $this->postdest = $dict["postdest"];
            $this->dring = $dict["dring"];
            $this->remotealert_id = $dict["remotealert_id"];
            $this->needsconf = $dict["needsconf"];
            $this->toolate_id = $dict["toolate_id"];
            $this->pre_ring = $dict["pre_ring"];
            $this->ringing = $dict["ringing"];
        } else if ($flag === "delete" || $flag === "select") {
            $this->grpnum = $dict["grpnum"];
        }
    }

    public function insert_into_findmefollow_sqlscript()
    {
        $sql_script = "INSERT INTO findmefollow (grpnum, strategy, grptime, grppre, grplist, annmsg_id, postdest, dring, remotealert_id, needsconf, toolate_id, pre_ring, ringing) VALUES " .
            "('" . $this->grpnum . "', '" . $this->strategy . "', " . $this->grptime . ", '" . $this->grppre . "', '" . $this->grplist . "', " . $this->annmsg_id . ", '" . $this->postdest . "', '" . $this->dring . "', " . $this->remotealert_id . ", '" . $this->needsconf . "', " . $this->toolate_id . ", " . $this->pre_ring . ", '" . $this->ringing . "')";
        return $sql_script;
    }

    public function update_findmefollow_sqlscript()
    {
        $sql_script = "UPDATE findmefollow SET grpnum='" . $this->grpnum . "', strategy='" . $this->strategy . "', grptime=" . $this->grptime . ", grppre='" . $this->grppre . "', grplist='" . $this->grplist . "', annmsg_id=" . $this->annmsg_id . ", postdest='" . $this->postdest . "', dring='" . $this->dring . "', remotealert_id=" . $this->remotealert_id . ", needsconf='" . $this->needsconf . "', toolate_id=" . $this->toolate_id . ", pre_ring=" . $this->pre_ring . ", ringing='" . $this->ringing . "'";
        return $sql_script;
    }

    public function delete_findmefollow_sqlscript()
    {
        $sql_script = "DELETE FROM findmefollow WHERE grpnum='" . $this->grpnum . "'";
        return $sql_script;
    }

    public function select_findmefollow_sqlscript()
    {
        $sql_script = "SELECT * FROM findmefollow WHERE grpnum='" . $this->grpnum . "'";
        return $sql_script;
    }

    public function select_all_findmefollow_sqlscript()
    {
        $sql_script = "SELECT * FROM findmefollow";
        return $sql_script;
    }
}

?>