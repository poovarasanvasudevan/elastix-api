<?php
include_once dirname(__FILE__) . "/lib/ini/ini_handler.php";
include_once dirname(__FILE__) . "/lib/json/phpJson.class.php";
include_once dirname(__FILE__) . "/lib/ast/Extension.php";

class Elastix
{
    public function __construct()
    {
        $fh = fopen('/etc/elastix.conf', 'r');
        $data = array();
        while ($line = fgets($fh)) {
            if (strlen($line) > 1) {
                $doarr = split("=", $line);
                $passwd = (string)$doarr[1];
                $passwd = str_replace("\n", "", $passwd);
                $data[(string)$doarr[0]] = $passwd;
            }
        }
        fclose($fh);
        $this->hostname = "localhost";
        $this->username = "root";
        $this->password = $data["mysqlrootpwd"];
        $this->db = null;
    }

    public function __destruct()
    {
        try {
            $this->db = null;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    private function _get_db_connection($dbname)
    {
        try {
            $this->db = new PDO("mysql:host=" . $this->hostname . ";dbname=" . $dbname . ";charset=utf8", $this->username, $this->password);
            $this->db->query("SET CHARACTER SET utf8");
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    private function _cdr_where_expression($start_date, $end_date, $field_name, $field_pattern, $status, $custom)
    {
        $where = "";
        if (!is_null($start_date) && !is_null($end_date))
            $where .= "(calldate BETWEEN '$start_date' AND '$end_date')";

        if (!is_null($field_name) && !is_null($field_pattern)) {
            $where = (empty($where)) ? $where : "$where AND ";
            $where .= "($field_name LIKE '%$field_pattern%')";
        }

        $where = (empty($where)) ? $where : "$where AND ";
        $where .= (is_null($status) || empty($status) || $status === "ALL") ? "(disposition IN ('ANSWERED', 'BUSY', 'FAILED', 'NO ANSWER'))" : "(disposition = '$status')";
        $where .= " AND dst != 's' ";

        if (!is_null($custom))
            $where .= $custom;

        return $where;
    }

    public function get_cdr()
    {
        /*
            +---------------+
            | COLUMN_NAME   |
            +---------------+
            | calldate      |
            | clid          |
            | src           |
            | dst           |
            | dcontext      |
            | channel       |
            | dstchannel    |
            | lastapp       |
            | lastdata      |
            | duration      |
            | billsec       |
            | disposition   |
            | amaflags      |
            | accountcode   |
            | uniqueid      |
            | userfield     |
            | recordingfile |
            | cnum          |
            | cnam          |
            | outbound_cnum |
            | outbound_cnam |
            | dst_cnam      |
            | did           |
            +---------------+
        */
        try {
            $this->_get_db_connection("asteriskcdrdb");
            $start_date = $_POST["start_date"];
            $end_date = $_POST["end_date"];
            $field_name = $_POST["field_name"];
            $field_pattern = $_POST["field_pattern"];
            $status = $_POST["status"];
            $limit = isset($_POST["limit"]) ? $_POST["limit"] : 100;
            $custom = $_POST["custom"];
            $where_expression = $this->_cdr_where_expression($start_date, $end_date, $field_name, $field_pattern, $status, $custom);
            $sql_cmd = "SELECT * FROM cdr WHERE $where_expression ORDER BY calldate DESC LIMIT $limit";
            $stmt = $this->db->prepare($sql_cmd);
            $stmt->execute();
            $result = (array)$stmt->fetchAll(PDO::FETCH_ASSOC);
            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function get_wav_file()
    {
        /*
            $name = "/2015/12/08/out-05355620760-101-20151208-102449-1449563089.106.wav";
        */
        $name = $_GET["name"];
        $directory = "/var/spool/asterisk/monitor";
        $file = realpath($directory . $name);
        if (strpos($file, $directory) !== false && strpos($file, $directory) == 0 && file_exists($file) && is_file($file)) {
            header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
            header("Content-Length: " . filesize($file));
            header("Content-Type: application/octet-stream;");
            readfile($file);
        } else {
            header("HTTP/1.0 404 Not Found");
            header("Content-Type: application/json");
            echo '{"status": "File not found", "code": 404}';
        }

    }

    public function get_harddrivers()
    {
        $main_arr = array();
        exec("df -H /", $harddisk);
        exec("du -sh /var/log", $logs);
        exec("du -sh /opt", $thirdparty);
        exec("du -sh /var/spool/asterisk/voicemail", $voicemails);
        exec("du -sh /var/www/backup", $backups);
        exec("du -sh /etc", $configuration);
        exec("du -sh /var/spool/asterisk/monitor", $recording);
        $hard_arr = array();
        $tmp_arr = explode(" ", trim(preg_replace("/\s\s+/", " ", $harddisk[2])));
        $hard_arr["size"] = $tmp_arr[0];
        $hard_arr["used"] = $tmp_arr[1];
        $hard_arr["avail"] = $tmp_arr[2];
        $hard_arr["usepercent"] = $tmp_arr[3];
        $hard_arr["mount"] = $tmp_arr[4];
        $main_arr["harddisk"] = $hard_arr;
        $main_arr["logs"] = explode("\t", $logs[0]);
        $main_arr["thirdparty"] = explode("\t", $thirdparty[0]);
        $main_arr["voicemails"] = explode("\t", $voicemails[0]);
        $main_arr["backups"] = explode("\t", $backups[0]);
        $main_arr["configuration"] = explode("\t", $configuration[0]);
        $main_arr["recording"] = explode("\t", $recording[0]);
        header("Content-Type: application/json");
        echo json_encode($main_arr);
    }

    public function get_iptables_status()
    {
        $exist = 'false';
        $pid = shell_exec("sudo /sbin/service iptables status 2>&1");
        if (strlen($pid) > 100) {
            $exist = 'true';
        }
        header("Content-Type: application/json");
        echo '{"pid": "' . $pid . '", "is_exist": ' . $exist . '}';
    }

    private function apply_config()
    {
        exec("/var/lib/asterisk/bin/fwconsole reload", $data);
    }

    public function reload()
    {
        $this->apply_config();
        header('Content-Type: application/json');
        echo '{"status": "RELOAD OK", "code": 200}';
    }

    public function can_reload()
    {
        if (isset($_GET['reload']) && $_GET['reload'] == 'true') {
            return true;
        } else {
            return false;
        }
    }

    private function add_database_config($key, $value)
    {
        $cmd = "/usr/sbin/asterisk -rx 'database put " . $key . " \"" . $value . "\"" . "'";
        exec($cmd, $data);
    }

    private function delete_database_config($key)
    {
        $cmd = "/usr/sbin/asterisk -rx 'database del " . $key . "'";
        exec($cmd, $data);
    }

    public function dnd()
    {
        if ($_POST["status"] == "true") {
            $this->add_database_config("DND " . $_POST["extension"], "YES");
        } else {
            $this->delete_database_config("DND " . $_POST["extension"]);
        }

        header('Content-Type: application/json');
        echo '{"status": "OK", "code": 200}';
    }

    public function add_pjsip_extension()
    {
        $this->_get_db_connection("asterisk");
        $dict = array(
            "name" => $_POST["name"],
            "account" => $_POST["account"],
            "fingerprint" => isset($_POST["fingerprint"]) ? $_POST["fingerprint"] : "5",
            "accountcode" => $_POST["accountcode"],
            "aggregate_mwi" => isset($_POST["aggregate_mwi"]) ? $_POST["aggregate_mwi"] : "yes",
            "allow" => $_POST["allow"],
            "avpf" => isset($_POST["avpf"]) ? $_POST["avpf"] : "no",
            "bundle" => isset($_POST["bundle"]) ? $_POST["bundle"] : "no",
            "callerid" => isset($_POST["callerid"]) ? $_POST["callerid"] : $_POST["account"] . " <" . $_POST["account"] . ">",
            "context" => isset($_POST["context"]) ? $_POST["context"] : "from-internal",
            "defaultuser" => $_POST["defaultuser"],
            "device_state_busy_at" => isset($_POST["device_state_busy_at"]) ? $_POST["device_state_busy_at"] : "0",
            "dial" => isset($_POST["dial"]) ? $_POST["dial"] : "PJSIP/" . $_POST["account"],
            "direct_media" => isset($_POST["direct_media"]) ? $_POST["direct_media"] : "yes",
            "disallow" => isset($_POST["disallow"]) ? $_POST["disallow"] : "",
            "dtmfmode" => isset($_POST["dtmfmode"]) ? $_POST["dtmfmode"] : "rfc4733",
            "force_rport" => isset($_POST["force_rport"]) ? $_POST["force_rport"] : "yes",
            "icesupport" => isset($_POST["icesupport"]) ? $_POST["icesupport"] : "no",
            "match" => isset($_POST["match"]) ? $_POST["match"] : "",
            "max_audio_streams" => isset($_POST["max_audio_streams"]) ? $_POST["max_audio_streams"] : "1",
            "max_contacts" => isset($_POST["max_contacts"]) ? $_POST["max_contacts"] : "1",
            "max_video_streams" => isset($_POST["max_video_streams"]) ? $_POST["max_video_streams"] : "1",
            "maximum_expiration" => isset($_POST["maximum_expiration"]) ? $_POST["maximum_expiration"] : "7200",
            "media_encryption" => isset($_POST["media_encryption"]) ? $_POST["media_encryption"] : "no",
            "media_encryption_optimistic" => isset($_POST["media_encryption_optimistic"]) ? $_POST["media_encryption_optimistic"] : "no",
            "media_use_received_transport" => isset($_POST["media_use_received_transport"]) ? $_POST["media_use_received_transport"] : "no",
            "message_context" => isset($_POST["message_context"]) ? $_POST["message_context"] : "",
            "minimum_expiration" => isset($_POST["minimum_expiration"]) ? $_POST["minimum_expiration"] : "60",
            "mwi_subscription" => isset($_POST["mwi_subscription"]) ? $_POST["mwi_subscription"] : "auto",
            "namedcallgroup" => isset($_POST["namedcallgroup"]) ? $_POST["namedcallgroup"] : "",
            "namedpickupgroup" => isset($_POST["namedpickupgroup"]) ? $_POST["namedpickupgroup"] : "",
            "outbound_auth" => isset($_POST["outbound_auth"]) ? $_POST["outbound_auth"] : "yes",
            "outbound_proxy" => isset($_POST["outbound_proxy"]) ? $_POST["outbound_proxy"] : "",
            "qualifyfreq" => isset($_POST["qualifyfreq"]) ? $_POST["qualifyfreq"] : "60",
            "refer_blind_progress" => isset($_POST["refer_blind_progress"]) ? $_POST["refer_blind_progress"] : "yes",
            "remove_existing" => isset($_POST["remove_existing"]) ? $_POST["remove_existing"] : "yes",
            "rewrite_contact" => isset($_POST["rewrite_contact"]) ? $_POST["rewrite_contact"] : "yes",
            "rtcp_mux" => isset($_POST["rtcp_mux"]) ? $_POST["rtcp_mux"] : "no",
            "rtp_symmetric" => isset($_POST["rtp_symmetric"]) ? $_POST["rtp_symmetric"] : "yes",
            "rtp_timeout" => isset($_POST["rtp_timeout"]) ? $_POST["rtp_timeout"] : "0",
            "rtp_timeout_hold" => isset($_POST["rtp_timeout_hold"]) ? $_POST["rtp_timeout_hold"] : "0",
            "secret" => $_POST["secret"],
            "secret_origional" => "",
            "send_connected_line" => isset($_POST["send_connected_line"]) ? $_POST["send_connected_line"] : "yes",
            "sendrpid" => isset($_POST["sendrpid"]) ? $_POST["sendrpid"] : "pai",
            "sipdriver" => isset($_POST["sipdriver"]) ? $_POST["sipdriver"] : "chan_pjsip",
            "timers" => isset($_POST["timers"]) ? $_POST["timers"] : "yes",
            "timers_min_se" => isset($_POST["timers_min_se"]) ? $_POST["timers_min_se"] : "90",
            "transport" => isset($_POST["transport"]) ? $_POST["transport"] : "",
            "trustrpid" => isset($_POST["trustrpid"]) ? $_POST["trustrpid"] : "yes",
            "user_eq_phone" => isset($_POST["user_eq_phone"]) ? $_POST["user_eq_phone"] : "no", ̥
        );
        $ext = new Extension($dict, "pjsip_insert");
        $stmt0 = $this->db->prepare($ext->select_sip_sqlscript());
        $stmt0->execute();
        $row = $stmt0->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            $stmt1 = $this->db->exec($ext->insert_into_pjsip_users_sqlscript());
            $stmt2 = $this->db->exec($ext->insert_into_pjsip_devices_sqlscript());
            $stmt3 = $this->db->exec($ext->insert_into_pjsip_sqlscript());
            $stmt4 = $this->db->exec($ext->insert_pjsip_cert());
            $ext->insert_pjsip_db_config();

            if ($this->can_reload()) {
                $this->apply_config();
            }
        }

        header('Content-Type: application/json');
        echo '{"status": "INSERT OK", "code": 200}';
    }


    public function update_pjsip_extension()
    {
        $this->_get_db_connection("asterisk");
        $dict = array(
            "account" => $_POST["account"],
            "accountcode" => $_POST["accountcode"],
            "fingerprint" => isset($_POST["fingerprint"]) ? $_POST["fingerprint"] : "5",
            "name" => $_POST["name"],
            "aggregate_mwi" => isset($_POST["aggregate_mwi"]) ? $_POST["aggregate_mwi"] : "yes",
            "allow" => $_POST["allow"],
            "avpf" => isset($_POST["avpf"]) ? $_POST["avpf"] : "no",
            "bundle" => isset($_POST["bundle"]) ? $_POST["bundle"] : "no",
            "callerid" => isset($_POST["callerid"]) ? $_POST["callerid"] : $_POST["account"] . " <" . $_POST["account"] . ">",
            "context" => isset($_POST["context"]) ? $_POST["context"] : "from-internal",
            "defaultuser" => $_POST["defaultuser"],
            "device_state_busy_at" => isset($_POST["device_state_busy_at"]) ? $_POST["device_state_busy_at"] : "0",
            "dial" => isset($_POST["dial"]) ? $_POST["dial"] : "PJSIP/" . $_POST["account"],
            "direct_media" => isset($_POST["direct_media"]) ? $_POST["direct_media"] : "yes",
            "disallow" => isset($_POST["disallow"]) ? $_POST["disallow"] : "",
            "dtmfmode" => isset($_POST["dtmfmode"]) ? $_POST["dtmfmode"] : "rfc4733",
            "force_rport" => isset($_POST["force_rport"]) ? $_POST["force_rport"] : "yes",
            "icesupport" => isset($_POST["icesupport"]) ? $_POST["icesupport"] : "no",
            "match" => isset($_POST["match"]) ? $_POST["match"] : "",
            "max_audio_streams" => isset($_POST["max_audio_streams"]) ? $_POST["max_audio_streams"] : "1",
            "max_contacts" => isset($_POST["max_contacts"]) ? $_POST["max_contacts"] : "1",
            "max_video_streams" => isset($_POST["max_video_streams"]) ? $_POST["max_video_streams"] : "1",
            "maximum_expiration" => isset($_POST["maximum_expiration"]) ? $_POST["maximum_expiration"] : "7200",
            "media_encryption" => isset($_POST["media_encryption"]) ? $_POST["media_encryption"] : "no",
            "media_encryption_optimistic" => isset($_POST["media_encryption_optimistic"]) ? $_POST["media_encryption_optimistic"] : "no",
            "media_use_received_transport" => isset($_POST["media_use_received_transport"]) ? $_POST["media_use_received_transport"] : "no",
            "message_context" => isset($_POST["message_context"]) ? $_POST["message_context"] : "",
            "minimum_expiration" => isset($_POST["minimum_expiration"]) ? $_POST["minimum_expiration"] : "60",
            "mwi_subscription" => isset($_POST["mwi_subscription"]) ? $_POST["mwi_subscription"] : "auto",
            "namedcallgroup" => isset($_POST["namedcallgroup"]) ? $_POST["namedcallgroup"] : "",
            "namedpickupgroup" => isset($_POST["namedpickupgroup"]) ? $_POST["namedpickupgroup"] : "",
            "outbound_auth" => isset($_POST["outbound_auth"]) ? $_POST["outbound_auth"] : "yes",
            "outbound_proxy" => isset($_POST["outbound_proxy"]) ? $_POST["outbound_proxy"] : "",
            "qualifyfreq" => isset($_POST["qualifyfreq"]) ? $_POST["qualifyfreq"] : "60",
            "refer_blind_progress" => isset($_POST["refer_blind_progress"]) ? $_POST["refer_blind_progress"] : "yes",
            "remove_existing" => isset($_POST["remove_existing"]) ? $_POST["remove_existing"] : "yes",
            "rewrite_contact" => isset($_POST["rewrite_contact"]) ? $_POST["rewrite_contact"] : "yes",
            "rtcp_mux" => isset($_POST["rtcp_mux"]) ? $_POST["rtcp_mux"] : "no",
            "rtp_symmetric" => isset($_POST["rtp_symmetric"]) ? $_POST["rtp_symmetric"] : "yes",
            "rtp_timeout" => isset($_POST["rtp_timeout"]) ? $_POST["rtp_timeout"] : "0",
            "rtp_timeout_hold" => isset($_POST["rtp_timeout_hold"]) ? $_POST["rtp_timeout_hold"] : "0",
            "secret" => $_POST["secret"],
            "secret_origional" => "",
            "send_connected_line" => isset($_POST["send_connected_line"]) ? $_POST["send_connected_line"] : "yes",
            "sendrpid" => isset($_POST["sendrpid"]) ? $_POST["sendrpid"] : "pai",
            "sipdriver" => isset($_POST["sipdriver"]) ? $_POST["sipdriver"] : "chan_pjsip",
            "timers" => isset($_POST["timers"]) ? $_POST["timers"] : "yes",
            "timers_min_se" => isset($_POST["timers_min_se"]) ? $_POST["timers_min_se"] : "90",
            "transport" => isset($_POST["transport"]) ? $_POST["transport"] : "",
            "trustrpid" => isset($_POST["trustrpid"]) ? $_POST["trustrpid"] : "yes",
            "user_eq_phone" => isset($_POST["user_eq_phone"]) ? $_POST["user_eq_phone"] : "no", ̥
        );
        $ext = new Extension($dict, "update");
        $stmt1 = $this->db->exec($ext->update_pjsip_sqlscript());
        $stmt2 = $this->db->exec($ext->update_pjsip_users_sqlscript());
        if ($this->can_reload()) {
            $this->apply_config();
        }
        header('Content-Type: application/json');
        echo '{"status": "UPDATE OK", "code": 200}';
    }

    public function add_sip_extension()
    {
        $this->_get_db_connection("asterisk");
        $dict = array(
            "name" => $_POST["name"],
            "deny" => $_POST["deny"],
            "secret" => $_POST["secret"],
            "dtmfmode" => $_POST["dtmfmode"],
            "canreinvite" => $_POST["canreinvite"],
            "context" => $_POST["context"],
            "host" => $_POST["host"],
            "trustrpid" => $_POST["trustrpid"],
            "sendrpid" => $_POST["sendrpid"],
            "type" => $_POST["type"],
            "nat" => $_POST["nat"],
            "port" => $_POST["port"],
            "qualify" => $_POST["qualify"],
            "qualifyfreq" => $_POST["qualifyfreq"],
            "transport" => $_POST["transport"],
            "avpf" => $_POST["avpf"],
            "icesupport" => $_POST["icesupport"],
            "encryption" => $_POST["encryption"],
            "callgroup" => $_POST["callgroup"],
            "pickupgroup" => $_POST["pickupgroup"],
            "dial" => $_POST["dial"],
            "mailbox" => $_POST["mailbox"],
            "permit" => $_POST["permit"],
            "callerid" => $_POST["callerid"],
            "callcounter" => $_POST["callcounter"],
            "faxdetect" => $_POST["faxdetect"],
            "account" => $_POST["account"]
        );
        $ext = new Extension($dict, "insert");
        $stmt0 = $this->db->prepare($ext->select_sip_sqlscript());
        $stmt0->execute();
        $row = $stmt0->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            $stmt1 = $this->db->exec($ext->insert_into_users_sqlscript());
            $stmt2 = $this->db->exec($ext->insert_into_devices_sqlscript());
            $stmt3 = $this->db->exec($ext->insert_into_sip_sqlscript());
            $this->apply_config();
        }
        header('Content-Type: application/json');
        echo '{"status": "INSERT OK", "code": 200}';
    }

    public function update_sip_extension()
    {
        $this->_get_db_connection("asterisk");
        $dict = array(
            "name" => $_POST["name"],
            "deny" => $_POST["deny"],
            "secret" => $_POST["secret"],
            "dtmfmode" => $_POST["dtmfmode"],
            "canreinvite" => $_POST["canreinvite"],
            "context" => $_POST["context"],
            "host" => $_POST["host"],
            "trustrpid" => $_POST["trustrpid"],
            "sendrpid" => $_POST["sendrpid"],
            "type" => $_POST["type"],
            "nat" => $_POST["nat"],
            "port" => $_POST["port"],
            "qualify" => $_POST["qualify"],
            "qualifyfreq" => $_POST["qualifyfreq"],
            "transport" => $_POST["transport"],
            "avpf" => $_POST["avpf"],
            "icesupport" => $_POST["icesupport"],
            "encryption" => $_POST["encryption"],
            "callgroup" => $_POST["callgroup"],
            "pickupgroup" => $_POST["pickupgroup"],
            "dial" => $_POST["dial"],
            "mailbox" => $_POST["mailbox"],
            "permit" => $_POST["permit"],
            "callerid" => $_POST["callerid"],
            "callcounter" => $_POST["callcounter"],
            "faxdetect" => $_POST["faxdetect"],
            "account" => $_POST["account"]
        );
        $ext = new Extension($dict, "update");
        $stmt1 = $this->db->exec($ext->update_sip_sqlscript());
        $stmt2 = $this->db->exec($ext->update_users_sqlscript());
        $this->apply_config();
        header('Content-Type: application/json');
        echo '{"status": "UPDATE OK", "code": 200}';
    }

    public function delete_sip_extension()
    {
        $this->_get_db_connection("asterisk");
        $dict = array("account" => $_POST["account"]);
        $ext = new Extension($dict, "delete");
        $stmt1 = $this->db->exec($ext->delete_sip_sqlscript());
        $stmt2 = $this->db->exec($ext->delete_users_sqlscript());
        $stmt3 = $this->db->exec($ext->delete_devices_sqlscript());
        $stmt4 = $this->db->exec($ext->delete_pjsip_cert());
        $ext->delete_pjsip_db_config();

        if ($this->can_reload()) {
            $this->apply_config();
        }
        header('Content-Type: application/json');
        echo '{"status": "DELETE OK", "code": 200}';
    }

    private function apply_retrieve()
    {
        exec("/var/lib/asterisk/bin/retrieve_conf", $data);
    }

    private function show_ampuser($dict)
    {
        exec('/usr/sbin/asterisk -rx "database show AMPUSER ' . $dict["grpnum"] . '/followme');
    }

    private function put_ampuser($dict)
    {
        exec('/usr/sbin/asterisk -rx "database put AMPUSER ' . $dict["grpnum"] . '/followme/changecid default"');
        exec('/usr/sbin/asterisk -rx "database put AMPUSER ' . $dict["grpnum"] . '/followme/ddial DIRECT"');
        exec('/usr/sbin/asterisk -rx "database put AMPUSER ' . $dict["grpnum"] . '/followme/fixedcid "');
        exec('/usr/sbin/asterisk -rx "database put AMPUSER ' . $dict["grpnum"] . '/followme/grpconf ENABLED"');
        exec('/usr/sbin/asterisk -rx "database put AMPUSER ' . $dict["grpnum"] . '/followme/grplist ' . $dict["grplist"] . '"');
        exec('/usr/sbin/asterisk -rx "database put AMPUSER ' . $dict["grpnum"] . '/followme/grptime ' . $dict["grptime"] . '"');
        exec('/usr/sbin/asterisk -rx "database put AMPUSER ' . $dict["grpnum"] . '/followme/prering ' . $dict["pre_ring"] . '"');
    }

    private function deltree_ampuser($dict)
    {
        exec('/usr/sbin/asterisk -rx "database deltree AMPUSER ' . $dict["grpnum"] . '/followme"');
    }

    public function add_followme_extension()
    {
        $this->_get_db_connection("asterisk");
        $dict = array(
            "grpnum" => $_POST["grpnum"],
            "strategy" => $_POST["strategy"],
            "grptime" => $_POST["grptime"],
            "grppre" => $_POST["grppre"],
            "grplist" => $_POST["grplist"],
            "annmsg_id" => $_POST["annmsg_id"],
            "postdest" => $_POST["postdest"],
            "dring" => $_POST["dring"],
            "remotealert_id" => $_POST["remotealert_id"],
            "needsconf" => $_POST["needsconf"],
            "toolate_id" => $_POST["toolate_id"],
            "pre_ring" => $_POST["pre_ring"],
            "ringing" => $_POST["ringing"]
        );
        $this->put_ampuser($dict);
        $find = new FindMeFollow($dict, "insert");
        $stmt0 = $this->db->prepare($find->select_findmefollow_sqlscript());
        $stmt0->execute();
        $row = $stmt0->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            $stmt1 = $this->db->exec($find->insert_into_findmefollow_sqlscript());
            $this->apply_retrieve();
            $this->apply_config();
        }
        header('Content-Type: application/json');
        echo '{"status": "INSERT OK", "code": 200}';
    }

    public function update_followme_extension()
    {
        $this->_get_db_connection("asterisk");
        $dict = array(
            "grpnum" => $_POST["grpnum"],
            "strategy" => $_POST["strategy"],
            "grptime" => $_POST["grptime"],
            "grppre" => $_POST["grppre"],
            "grplist" => $_POST["grplist"],
            "annmsg_id" => $_POST["annmsg_id"],
            "postdest" => $_POST["postdest"],
            "dring" => $_POST["dring"],
            "remotealert_id" => $_POST["remotealert_id"],
            "needsconf" => $_POST["needsconf"],
            "toolate_id" => $_POST["toolate_id"],
            "pre_ring" => $_POST["pre_ring"],
            "ringing" => $_POST["ringing"]
        );
        $this->put_ampuser($dict);
        $find = new FindMeFollow($dict, "update");
        $stmt1 = $this->db->exec($find->update_findmefollow_sqlscript());
        $this->apply_retrieve();
        $this->apply_config();
        header('Content-Type: application/json');
        echo '{"status": "UPDATE OK", "code": 200}';
    }

    public function delete_followme_extension()
    {
        $this->_get_db_connection("asterisk");
        $dict = array("grpnum" => $_POST["grpnum"]);
        $this->deltree_ampuser($dict);
        $find = new FindMeFollow($dict, "delete");
        $stmt1 = $this->db->exec($find->delete_findmefollow_sqlscript());
        $this->apply_retrieve();
        $this->apply_config();
        header('Content-Type: application/json');
        echo '{"status": "DELETE OK", "code": 200}';
    }

    public function view_followme_extension()
    {
        $this->_get_db_connection("asterisk");
        $dict = array("grpnum" => $_POST["grpnum"]);
        $find = new FindMeFollow($dict, "select");
        $stmt1 = $this->db->prepare($find->select_findmefollow_sqlscript());
        $stmt1->execute();
        $result = (array)$stmt1->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function view_followme_all_extensions()
    {
        $this->_get_db_connection("asterisk");
        $dict = array();
        $find = new FindMeFollow($dict, "selectall");
        $stmt1 = $this->db->prepare($find->select_all_findmefollow_sqlscript());
        $stmt1->execute();
        $result = (array)$stmt1->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($result);
    }
}

?>
