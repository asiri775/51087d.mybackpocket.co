<?php


namespace App\Helpers;


use Symfony\Component\Process\Process;

class Helper
{

    public static function slugifyText($text)
    {
        $text = str_replace(' ', '_', $text);
        $text = strtolower($text);
        $text = trim($text);
        return $text;
    }

    public static function displayDateTime($time)
    {
        if ($time == null) {
            return '-';
        }
        $time = strtotime($time);
        return date('d M Y h:i A', $time);
    }

    public static function displayDate($time)
    {
        $time = strtotime($time);
        return date('d M Y', $time);
    }

    public static function printBadge(string $text, string $class = 'badge badge-info'): string
    {
        return '<span class="' . $class . '">' . $text . '</span>';
    }

    public static function getActionButtons($links): string
    {
        $html = '<div class="table-actions"><div class="btn-group">';
        if (is_array($links) && count($links) > 0) {
            foreach ($links as $action => $link) {
                if (array_key_exists('url', $link)) {
                    $url = $link['url'];
                    $icon = $text = '';
                    $class = 'btn-complete';
                    if (array_key_exists('text', $link)) {
                        $text = $link['text'];
                    } else {
                        $text = ucwords($action);
                    }
                    if (array_key_exists('icon', $link)) {
                        $icon = $link['icon'];
                    } else {
                        switch ($action) {
                            case 'view':
                                $icon = 'fa fa-eye';
                                $class = 'btn btn-complete';
                                break;
                            case 'edit':
                                $icon = 'fa fa-edit';
                                $class = 'btn btn-info';
                                break;
                            case 'change-password':
                                $icon = 'fa fa-lock';
                                $class = 'btn btn-complete';
                                break;
                            case 'delete':
                                $icon = 'fa fa-trash-o';
                                $class = 'btn btn-danger';
                                break;
                        }
                    }

                    $is_form = false;
                    if (array_key_exists('is_form', $link)) {
                        $is_form = $link['is_form'];
                    }

                    if (array_key_exists('class', $link)) {
                        $class = $class . " " . $link['class'];
                    }

                    $dataAttributesHtml = '';
                    if (array_key_exists('dataAttributes', $link)) {
                        $dataAttributes = $link['dataAttributes'];
                        if (is_array($dataAttributes) && count($dataAttributes) > 0) {
                            foreach ($dataAttributes as $key => $value) {
                                $value = (string)$value;
                                $dataAttributesHtml .= ' data-' . $key . '="' . $value . '" ';
                            }
                        }
                    }

                    if ($is_form) {
                        $html .= '<form class="delete-modal-form" action="' . $url . '" method="POST">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                                <button ' . $dataAttributesHtml . ' type="submit" class="btn btn-xs ' . $class . '" title="' . $text . '"><i class="' . $icon . '"></i></button>
                            </form>';
                    } else {
                        $html .= '<a ' . $dataAttributesHtml . ' class="btn btn-xs ' . $class . '" href="' . $url . '" title="' . $text . '"><i class="' . $icon . '"></i></a>';
                    }
                }
            }
        }
        $html .= '</div></div>';
        return $html;
    }

    public static function formatText(string $text): string
    {
        $text = str_replace('_', ' ', $text);
        return ucwords($text);
    }

    public static function printAmount($amount): string
    {
        if ($amount != null && $amount > 0) {
            $amount = number_format($amount, 2);
        }

        if ($amount <= 0) {
            $amount = number_format($amount, 2);
        }

        return '$' . $amount;
    }

    public static function printAmountExport($amount): string
    {
        if ($amount != null && $amount > 0) {
            $amount = number_format($amount, 2);
        }

        if($amount <= 0){
            $amount = number_format($amount, 2);
        }

        return '$      ' . $amount;
    }

    public static function cropPDF($inputFile, $outputFile, $left = 0, $right = 0, $top = 0, $bottom = 0): string
    {
        $commend = "/usr/bin/pdfcrop --margins '-" . $left . " -" . $top . " -" . $right . " -" . $bottom . "' --clip " . $inputFile . " " . $outputFile;
        //echo $commend;die;
        $process = Process::fromShellCommandline($commend);
        $process->run();
        if (!$process->isSuccessful()) {
            return false;
        }
        return true;
    }

    public static function cleanArray(array $values)
    {
        foreach ($values as $key => $value) {
            $value = trim($value);
            if ($value == null) {
                unset($values[$key]);
            }
        }
        $values = array_values($values);
        return $values;
    }

    public static function convertToDate(string $date, string $year)
    {
        //echo $date." -> ";
        $date = $date . " " . $year;
        $date = strtotime($date);
        return $date;
    }

    public static function IfNullSlash($value)
    {
        if ($value == null) {
            return '-';
        }
        return $value;
    }

    public static function cleanIllegalThings($content)
    {
        if (is_array($content) && count($content) > 0) {

        }
        return $content;
    }

    public static function formatNumber($number)
    {
        try {
            return number_format(($number) ? $number : 0, 2, '.', '');
        } catch (\Exception $ex) {
            return '-';
        }
    }

}
