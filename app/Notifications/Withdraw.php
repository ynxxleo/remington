<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\EmailTemplate as ET;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Withdraw extends Notification implements ShouldQueue
{
    use Queueable;

    protected $tnx_data = null;
    protected $withd = null;
    protected $template = null;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($tnx_data, $withd, $template)
    {
        $this->tnx_data = $tnx_data;
        $this->withd = $withd;
        $this->template = $template;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $from_name = email_setting('from_name', get_setting('site_name'));
        $from_email = email_setting('from_email', get_setting('site_email'));

        $isBankWithdrawal = strcasecmp($this->withd->method->name, 'Bank Withdrawal') === 0;
        $templateSlug = $isBankWithdrawal ? 'bank-withdraw-'.$this->template : 'withdraw-'.$this->template;
        $template = ET::get_template($templateSlug);
        $transaction = $this->tnx_data;
        $user = $this->tnx_data->user;

        if ($isBankWithdrawal && empty(trim((string) $template->message))) {
            $template->message = $this->bankWithdrawalMessage($this->template === 'admin-approved' ? 'Approved' : 'Pending');
        }

        $template->message = $this->replace_shortcode($template->message);
        $template->regards = ($template->regards == 'true' ? get_setting('site_mail_footer', "Best Regards, \n[[site_name]]") : '');

        return (new MailMessage)
                    ->greeting($this->replace_shortcode($template->greeting))
                    ->salutation($this->replace_shortcode($template->regards))
                    ->from($from_email, $from_name)
                    ->subject($this->replace_shortcode($template->subject))
                    ->markdown('mail.transaction', compact('template', 'transaction','user'));
    }

    protected function bankWithdrawalMessage($status = 'Pending')
    {
        $details = (array) ($this->withd->withdraw_information ?? []);
        $rows = [];
        foreach ($details as $key => $value) {
            $value = is_object($value) ? ($value->field_name ?? '') : (is_array($value) ? ($value['field_name'] ?? '') : $value);
            $label = ucwords(str_replace('_', ' ', $key));
            $sensitive = in_array(strtolower($key), ['account_number', 'routing_number', 'iban', 'sort_code', 'swift_code'], true);
            if ($sensitive) {
                $digits = preg_replace('/\D+/', '', (string) $value);
                $value = $digits !== '' ? str_repeat('*', max(0, strlen($digits) - 4)).substr($digits, -4) : 'Not provided';
            }
            $rows[] = '<tr><td style="padding:6px 12px 6px 0;color:#68757e"><strong>'.e($label).'</strong></td><td style="padding:6px 0">'.e((string) $value).'</td></tr>';
        }

        return '<p>Your bank withdrawal request has been submitted and is now pending review.</p>'
            .'<p><strong>Withdrawal details</strong></p>'
            .'<table><tr><td style="padding:6px 12px 6px 0;color:#68757e"><strong>Amount</strong></td><td style="padding:6px 0">'.e(getAmount($this->tnx_data->amount).' '.$this->tnx_data->currency).'</td></tr>'
            .'<tr><td style="padding:6px 12px 6px 0;color:#68757e"><strong>Reference</strong></td><td style="padding:6px 0">'.e($this->tnx_data->trx).'</td></tr>'
            .'<tr><td style="padding:6px 12px 6px 0;color:#68757e"><strong>Status</strong></td><td style="padding:6px 0">'.e($status).'</td></tr></table>'
            .'<p><strong>Bank details</strong></p><table>'.implode('', $rows).'</table>';
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    /**
     * Get the short-code and replace with data.
     *
     * @param  mixed  $code
     * @return void
     */
    public function replace_shortcode($code)
    {
        $shortcode =array(
            "\n",
            '[[site_name]]',
            '[[site_email]]',
            '[[site_url]]',

            '[[amount]]',
            '[[currency]]',
            '[[method_name]]',
            '[[charge]]',
            '[[rate]]',
            '[[method_currency]]',
            '[[method_amount]]',
            '[[trx]]',
            '[[delay]]',
            '[[post_balance]]',
            '[[user_name]]',
            '[[user_email]]',
            '[[admin_details]]',
            '[[bank_details]]',
            '[[bank_account_name]]',
            '[[bank_account_number]]',
        );
        $replace = array(
            "<br>",
            site_info('name', false),
            site_info('email', false),
            url('/'),

            getAmount($this->tnx_data->amount),
            $this->tnx_data->currency,
            $this->withd->method->name,
            getAmount($this->tnx_data->charge),
            getAmount($this->withd->rate),
            $this->withd->method_currency,
            getAmount($this->withd->method_amount),
            $this->tnx_data->trx,
            $this->withd->method->delay,
            getAmount($this->tnx_data->post_balance),
            $this->tnx_data->user->name,
            $this->tnx_data->user->email,
            $this->withd->admin_feedback,
            $this->bankDetailsHtml(),
            $this->bankDetailValue(['account_name', 'account holder', 'name']),
            $this->bankDetailValue(['account_number', 'account']),

        );
        $return = str_replace($shortcode, $replace, $code);
        return $return;
    }

    protected function bankDetailsHtml()
    {
        $details = (array) ($this->withd->withdraw_information ?? []);
        $rows = [];
        foreach ($details as $key => $value) {
            $value = is_object($value) ? ($value->field_name ?? '') : (is_array($value) ? ($value['field_name'] ?? '') : $value);
            $digits = preg_replace('/\D+/', '', (string) $value);
            if (in_array(strtolower($key), ['account_number', 'routing_number', 'iban', 'sort_code', 'swift_code'], true)) {
                $value = $digits !== '' ? str_repeat('*', max(0, strlen($digits) - 4)).substr($digits, -4) : 'Not provided';
            }
            $rows[] = '<tr><td><strong>'.e(ucwords(str_replace('_', ' ', $key))).'</strong></td><td>'.e((string) $value).'</td></tr>';
        }
        return '<table>'.implode('', $rows).'</table>';
    }

    protected function bankDetailValue(array $keys)
    {
        $details = (array) ($this->withd->withdraw_information ?? []);
        foreach ($details as $name => $value) {
            $rawValue = is_object($value) ? ($value->field_name ?? '') : (is_array($value) ? ($value['field_name'] ?? '') : $value);
            $normalizedName = strtolower(str_replace(['-', ' '], '_', (string) $name));
            $normalizedLabel = strtolower(str_replace(['-', ' '], '_', (string) ($value->field_level ?? ($value['field_level'] ?? $name))));
            foreach ($keys as $key) {
                if ($normalizedName !== $key && $normalizedLabel !== $key) continue;
                if (in_array($key, ['account_number', 'account'], true)) {
                    $digits = preg_replace('/\D+/', '', (string) $rawValue);
                    return $digits !== '' ? str_repeat('*', max(0, strlen($digits) - 4)).substr($digits, -4) : 'Not provided';
                }
                return e((string) $rawValue);
            }
        }
        return 'Not provided';
    }
}
