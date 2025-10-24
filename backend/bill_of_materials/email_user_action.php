<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require_once __DIR__ . '/../../vendor/autoload.php';

function sendUserActionEmail($userAction, $data)
{
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();

    $mailHost = $_ENV['MAIL_HOST'];
    $mailUsername = $_ENV['MAIL_USERNAME'];
    $mailPassword = $_ENV['MAIL_PASSWORD'];
    $smtpEncryption = $_ENV['MAIL_ENCRYPTION'];
    $mailPort = $_ENV['MAIL_PORT'];

    $mailFrom = $_ENV['MAIL_FROM'];
    $mailFromName = $_ENV['MAIL_FROM_NAME'];

    $mailTo = $_ENV['MAIL_TO'];
    $mailToNames = $_ENV['MAIL_TO_NAMES'];

    if (!$mailHost || !$mailUsername || !$mailPassword || !$smtpEncryption || !$mailPort || !$mailFrom) {
        throw new Exception("Missing mail configuration in environment variables.");
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $mailHost;
        $mail->SMTPAuth = true;
        $mail->Username = $mailUsername;
        $mail->Password = $mailPassword;
        $mail->SMTPSecure = $smtpEncryption;
        $mail->Port = $mailPort;

        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';

        $mail->setFrom($mailFrom, $mailFromName);

        $emails = explode(',', $mailTo);
        $names = explode(',', $mailToNames);

        foreach ($emails as $index => $email) {
            $name = $names[$index] ?? '';
            $mail->addAddress(trim($email), trim($name));
        }

        $bodyContent = '';

        if ($userAction == "add") {
            $bodyContent .= 'The following items have been added to the Bill of Materials:<br><br>';
            $bodyContent .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">';
            $bodyContent .= '<thead>
                    <tr>
                        <th>Customer</th>
                        <th>Model</th>
                        <th>Master Code</th>
                        <th>Item Name</th>
                        <th>Tool Num</th>
                    </tr>
                </thead>';
            $bodyContent .= '<tbody>';

            foreach ($data as $item) {
                $bodyContent .= '<tr>';
                $bodyContent .= '<td>' . $item[0] . '</td>';
                $bodyContent .= '<td>' . $item[1] . '</td>';
                $bodyContent .= '<td>' . $item[2] . '</td>';
                $bodyContent .= '<td>' . $item[3] . '</td>';
                $bodyContent .= '<td>' . $item[4] . '</td>';
                $bodyContent .= '</tr>';
            }

            $bodyContent .= '</tbody></table>';
        } else if ($userAction == "edit") {
            $itemCode = $data[0][0][0];
            $itemName = $data[0][0][1];
            $itemTool = $data[0][0][2];

            $bodyContent .= '
                The following changes have been made to the item in the Bill of Materials:
                <br><br>
                <table cellpadding="2" cellspacing="0" style="border-collapse: collapse;">
                    <tr>
                        <td style="font-weight: bold;">Item Code:</td>
                        <td>' . $itemCode . '</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Item Name:</td>
                        <td>' . $itemName . '</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Tool Number:</td>
                        <td>' . $itemTool . '</td>
                    </tr>
                </table>
                <br>
            ';

            $type = $data[0][1] == 0 ? "Part" : "Material";

            $bodyContent .= 'The ' . $type . ' info have been updated in the Bill of Materials:<br><br>';
            $bodyContent .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">';
            $bodyContent .= '<thead>
                    <tr>
                        <th>Row Type</th>
                        <th>Column</th>
                        <th>Previous Value</th>
                        <th>New Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>';
            $bodyContent .= '<tbody>';

            foreach ($data as $item) {
                $bodyContent .= '<tr>';
                $bodyContent .= '<td>' . $type . '</td>';
                $bodyContent .= '<td>' . $item[2] . '</td>';
                $bodyContent .= '<td>' . $item[3] . '</td>';
                $bodyContent .= '<td>' . $item[4] . '</td>';
                $bodyContent .= '<td>' . $item[5] . '</td>';
                $bodyContent .= '</tr>';
            }

            $bodyContent .= '</tbody></table>';
        } else if ($userAction == "archive") {
            $type = $data[0][1];

            $bodyContent .= "The " . ucfirst($type) . " has been archived in the Bill of Materials:<br><br>";

            if ($type == "material") {
                $itemCode = $data[0][0][0];
                $itemName = $data[0][0][1];
                $itemTool = $data[0][0][2];

                $bodyContent .= '
                    Material is under the item below:
                    <br><br>
                    <table cellpadding="2" cellspacing="0" style="border-collapse: collapse;">
                        <tr>
                            <td style="font-weight: bold;">Item Code:</td>
                            <td>' . $itemCode . '</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Item Name:</td>
                            <td>' . $itemName . '</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Tool Number:</td>
                            <td>' . $itemTool . '</td>
                        </tr>
                    </table>
                    <br>
                ';
            }

            $bodyContent .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">';
            $bodyContent .= '<thead>
                    <tr>
                        <th>' . ucfirst($type) . ' Code</th>
                        <th>' . ucfirst($type) . ' Name</th>
                        ' . (($type == "part") ? '<th>Tool Num</th>' : '') . '             
                    </tr>
                </thead>';
            $bodyContent .= '<tbody>';

            $firstCol = $data[0][2][0] ?? '';
            $secondCol = $data[0][2][1] ?? '';
            $thirdCol = $data[0][2][2] ?? '';

            foreach ($data as $item) {
                $bodyContent .= '<tr>';
                $bodyContent .= '<td>' . $firstCol . '</td>';
                $bodyContent .= '<td>' . $secondCol . '</td>';

                if ($type == "part") {
                    $bodyContent .= '<td>' . $thirdCol . '</td>';
                }

                $bodyContent .= '</tr>';
            }

            $bodyContent .= '</tbody></table>';
        }

        $mail->Subject = 'BOM System Generated';
        $mail->isHTML(true);
        $mail->Body = '
        <b>This is an E-Mail sent via PTPI - SYSTEM auto-generated mail. Please do not reply.</b>
        <br><br>
        Dear Sir/Ma\'am,
        <br><br>  
        
        ' . $bodyContent . '
            
        <br><br>
        <b>--</b>
        <br><br>
        Best Regards,
        <br><br>
        <b>PRIMA TECH PHILS., INC.</b>
        <br>
        <b>Bill of Materials Management Systsem (BOM)</b>
    ';

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
