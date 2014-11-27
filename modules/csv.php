<?php

$pdf = new FPDF();
$pdf->SetAuthor('Some guy');
$pdf->SetTitle('Scam activity');
$pdf->SetFont('Helvetica', '', 8);
$pdf->SetTextColor(50, 60, 100);
$pdf->AddPage('P');

$pdf->SetDisplayMode(real, 'default');

$pdf->SetXY(10, 10);
$pdf->SetDrawColor(50, 60, 100);

foreach ($task_info[$task_id] as $key => $value) {
    $countries = '(' . implode(', ', $task_info[$task_id][$key]) . ')';
    $pdf->Cell(50, 5, $key . $countries, 1, 1, 'L', 0);
}
$pdf->Ln();

foreach ($response as $user) {
    if ($user['chats'][0]['message'] == "empty") {
        $error = 1;
        $pdf->SetFillColor(255, 0, 0);
    } else if ($user['count'] == 0) {
        $error = 1;
        $pdf->SetFillColor(255, 0, 0);
    } else {
        $error = 0;
        $pdf->SetFillColor(111, 255, 111);
    }
    $pdf->Cell(190, 3, 'Site: ' . $user['site'], 'LRT', 0, 'L', $error);
    $pdf->Ln();
    $pdf->Cell(190, 3, 'Country: ' . $user['country'], 'LR', 0, 'L', $error);
    $pdf->Ln();
    $pdf->Cell(190, 3, 'Id: ' . $user['id'], 'LR', 0, 'L', $error);
    $pdf->Ln();
    $pdf->Cell(190, 3, 'Mail: ' . $user['mail'], 'LR', 0, 'L', $error);
    $pdf->Ln();
    $pdf->Cell(190, 3, 'Gender: ' . $user['gender'], 'LR', 0, 'L', $error);
    $pdf->Ln();
    $pdf->Cell(190, 3, 'Register: ' . $user['register'], 'LR', 0, 'L', $error);
    $pdf->Ln();
    $pdf->Cell(190, 3, 'Platform: ' . $user['platform'], 'LR', 0, 'L', $error);
    $pdf->Ln();
    $pdf->Cell(190, 3, 'Birthday: ' . $user['birthday'] . '(' . $user['age'] . ')', 'LRB', 0, 'L', $error);
    $pdf->Ln();
    //var_dump($user['chats'][0]);

    if ($user['chats'][0]['message'] != "empty") {
        for ($i = 0; $i < sizeof($user['chats']); $i++) {
            if($user['chats'][$i]['user']['screenname'] == null && $user['chats'][$i]['user']['ll'] == 'no data' && $user['chats'][$i]['user']['distance_error'] == true) {
                $pdf->SetFillColor(255, 0, 0);
                $pdf->Cell(190, 3, 'User not found in DB: '.$user['chats'][$i]['user']['id'], 'LRB', 0, 'C', true);
                $pdf->Ln();
            } else {
                if (!$user['chats'][$i]['user'][99]) {
                    $pdf->SetFillColor(255, 255, 0);
                    $error = 2;
                } else {
                    $pdf->SetFillColor(111, 255, 111);
                    $error = 0;
                }
                
                $pdf->Cell(70, 3, 'Time: ' . $user['chats'][$i]['message']['time'], 'LT', 0, 'L', 1);
                if ($user['chats'][$i]['user']['distance_error'] == 1 && $user['chats'][$i]['user'][99]) {
                    $pdf->SetFillColor(255, 0, 0);
                }
                $pdf->Cell(20, 3, 'Dist: ' . $user['chats'][$i]['user']['distance'], 'T', 0, 'L', 1);
                switch ($error) {
                    case 0:
                        $pdf->SetFillColor(111, 255, 111);
                        break;
                    case 1:
                        $pdf->SetFillColor(255, 0, 0);
                        break;
                    case 2:
                        $pdf->SetFillColor(255, 255, 0);
                        break;
                }
                $pdf->Cell(20, 3, 'is99: ' . $user['chats'][$i]['user'][99], 'T', 0, 'L', 1);
                $pdf->Cell(20, 3, 'age: ' . $user['chats'][$i]['user']['age'] . '(' . abs($user['chats'][$i]['user']['age'] - $user['age']) . ')', 'T', 0, 'L', 1);
                $pdf->Cell(60, 3, 'id: ' . $user['chats'][$i]['user']['id'], 'RT', 0, 'L', 1);
                $pdf->Ln();
                if ($user['chats'][$i]['message']['message_error'] == 1 && $user['chats'][$i]['user'][99]) {
                    $pdf->SetFillColor(255, 0, 0);
                }
                $pdf->Cell(190, 3, iconv('UTF-8', 'cp1252', 'Message: ' . $user['chats'][$i]['message']['text']), 'LRB', 0, 'L', 1);
                $pdf->Ln();
            }
        }
    } else {
        $pdf->SetFillColor(255, 0, 0);
        $pdf->Cell(190, 3, 'No activity', 'LRB', 0, 'C', true);
        $pdf->Ln();
    }
    $pdf->Ln();
}

$pdf->Output('sc.pdf', 'I');
