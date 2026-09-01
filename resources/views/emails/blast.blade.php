<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; background-color:#FAF7F2; font-family: Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#FAF7F2; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.06);">

                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #1C1C1C 0%, #2D2D2D 100%); padding: 32px 40px; text-align:center;">
                            <p style="margin:0; font-size:22px; font-weight:700; color:#ffffff; letter-spacing:0.5px;">
                                {{ config('beauty-crm.company_name') }}
                            </p>
                            <p style="margin:6px 0 0; font-size:12px; color:#A0A0A0; letter-spacing:1.5px; text-transform:uppercase;">
                                Beauty & Wellness CRM
                            </p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin:0 0 20px; font-size:15px; color:#4A4A4A; line-height:1.7;">Halo!</p>

                            {{-- Isi Pesan (Rich Text HTML) --}}
                            <div style="font-size:15px; color:#2D2D2D; line-height:1.8;">{!! $content !!}</div>

                            {{-- Gambar Inline (CID Embed) — tidak butuh URL publik --}}
                            @if(!empty($imagePath) && file_exists(storage_path('app/public/' . $imagePath)))
                                <div style="margin-top: 28px; text-align: center;">
                                    {{--
                                        $message->embed() menyematkan gambar langsung ke dalam email
                                        sebagai CID (Content-ID) — berfungsi tanpa koneksi internet
                                        dari sisi server pengirim, dan tampil di semua email client.
                                    --}}
                                    <img src="{{ $message->embed(storage_path('app/public/' . $imagePath)) }}"
                                         alt="Gambar dari {{ config('beauty-crm.company_name') }}"
                                         style="max-width: 100%; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.10);">
                                </div>
                            @endif

                            <hr style="border:none; border-top:1px solid #F0ECE8; margin: 32px 0;">

                            <p style="margin:0; font-size:14px; color:#8A8A8A; line-height:1.6;">
                                Salam hangat,<br>
                                <strong style="color:#2D2D2D;">Tim {{ config('beauty-crm.company_name') }}</strong>
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#F5F1ED; padding: 20px 40px; text-align:center; border-top: 1px solid #EDE8E3;">
                            <p style="margin:0; font-size:11px; color:#A0A0A0;">
                                Email ini dikirim oleh sistem {{ config('beauty-crm.company_name') }} CRM.
                                Harap tidak membalas email ini.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
