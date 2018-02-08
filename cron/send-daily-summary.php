<?php

require_once __DIR__.'/../../../config/config.inc.php';
require_once __DIR__.'/../froggyhistory.php';

$date = date('Y-m-d');
$list = FroggyHistoryLog::getList(1, 1000, null, null, null, null, null, $date.' 00:00:01', $date.' 23:59:59');


$content = '"Product Name incl Combination";"Product Ref";"EAN";"Stock Level Was";"Stock Level Changed to";"Increase/Deduction of";"Date / Time Stamp of Change";"Employee";'."\n";
foreach ($list as $history) {
    if ($history['object'] == 'Product') {

        $employee = new Employee($history['id_employee']);
        Context::getContext()->employee = $employee;

        if (Validate::isLoadedObject($employee)) {
            $product = new Product($history['id_object'], true, Context::getContext()->language->id);
        }

        if (isset($product) && Validate::isLoadedObject($product)) {

            // Decode diff history
            $history['diff'] = json_decode($history['diff'], true);

            // If case with combination
            if (isset($history['diff']['Combinations'])) {

                // We loop over combinations
                foreach ($history['diff']['Combinations'] as $id_product_attribute => $combination) {

                    // If quantity update
                    if (isset($combination['Quantity'])) {

                        $combination_data = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'product_attribute` WHERE `id_product_attribute` = '.(int)$id_product_attribute);

                        $content .= buildFileLine([
                            $product->name.' '.$field_values['name'].' (#'.$product->id.' #'.$id_product_attribute.')',
                            $combination_data['reference'],
                            $combination_data['ean13'],
                            $combination['Quantity']['before'],
                            $combination['Quantity']['after'],
                            $combination['Quantity']['before'] - $combination['Quantity']['after'],
                            $history['date_add'],
                            $employee->firstname.' '.$employee->lastname.' (#'.$employee->id.')'
                        ]);
                    }
                }
            } else {
                if (isset($history['diff']['General quantity'])) {
                    $content .= buildFileLine([
                        $product->name.' (#'.$product->id.')',
                        $product->reference,
                        $product->ean13,
                        $history['diff']['General quantity']['before'],
                        $history['diff']['General quantity']['after'],
                        $history['diff']['General quantity']['before'] - $history['diff']['Quantity']['after'],
                        $history['date_add'],
                        $employee->firstname.' '.$employee->lastname.' (#'.$employee->id.')'
                    ]);
                }
            }
        }
    }
}

$file = __DIR__.'/daily-log.csv';
if (file_exists($file)) {
    unlink($file);
}
file_put_contents($file, $content);


function buildFileLine($tab)
{
    $line = '';
    foreach ($tab as $value) {
        $line .= '"'.$value.'";';
    }
    $line .= "\n";
    return $line;
}

dump($list);
exit;


$subject = '[FroggyHistory] Daily summary';
try {
    Mail::Send($id_lang, $template, $subject, $template_vars, $customer['email'], $customer['firstname'].' '.$customer['lastname'],
        Configuration::get('PS_SHOP_EMAIL'), Configuration::get('PS_SHOP_NAME'), NULL, NULL, __DIR__.'/../mails/');
    $customers_success[] = $customer;
} catch (\Exception $e) {
    $customer['error'] = $e->getMessage();
    $customers_failed[] = $customer;
}


die("OK\n");
