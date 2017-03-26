<?
$disabled = $order->order_status <> ComNucleonplusModelEntityOrder::STATUS_PROCESSING ? 'disabled="disabled' : null;
?>

<table>
    <tbody>
        <tr>
            <td style="width: 100px">Tracking #</td>
            <td>
                <input <?= $disabled ?> type="text" name="tracking_reference" value="<?= $order->tracking_reference ?>">
            </td>
        </tr>
    </tbody>
</table>