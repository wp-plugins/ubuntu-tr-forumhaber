=== Ubuntu TR Forum Haber ===
Contributors: alquirel
Donate link: http://www.ubuntu-tr.net
Tags: post, admin, news, ajax, metabox
Requires at least: 2.0
Tested up to: 3.3
Stable tag: 1.1

Ubuntu Türkiye forumlarındaki bir iletiyi, sadece bağlantısını kullanarak blogunuza yazı şeklinde ekler.

== Description ==

WordPress Yazı Ekle sayfasına bir kutu (meta box) yerleştirir. Ubuntu Türkiye forumlarında gördüğünüz, blogunuza yazı olarak eklemek istediğiniz herhangi bir iletiyi, sizden sadece bağlantısını isteyerek otomatik bir şekilde alır ve WordPress Yazı Ekle sayfasında "başlık" ve "içerik" kısımlarına ekler. Size de sadece son bir kontrol yapıp yazınızı kaydetmek kalır.

== Installation ==

1. `ubuntu-tr-forumhaber.php` dosyasını `/wp-content/plugins/` klasörüne taşıyın.
1. `Eklentiler` sayfasında eklentiyi etkinleştirin.
1. Eklenti otomatik olarak Yazı Ekle sayfasına kutusunu yerleştirecektir.

== Frequently Asked Questions ==

= İleti içeriğini hangi metodla alır? =

İleti içeriğini Ajax kullanarak alır. Sunucunuzda cURL paketi kurulu ise cURL ile, değilse `file_get_contents` fonksiyonu ile iletiyi okur.

= İletileri BBCode halinde mi alır? =

Hayır, iletiler BBCode'un işlenmiş HTML şekli olarak gelir. Bu işlenmiş HTML, SMF'ye aittir. Dilerseniz temanızın stil dosyasına SMF'nin BBCode için kullandığı `class` özelliklerine stiller girebilirsiniz.

== Screenshots ==

1. `/tags/1.1/screenshot-1.jpeg`

== Changelog ==

= 1.1 =

WP 3.0 öncesiyle uyumluluk özelliği eklendi.

= 1.0 =

Eklentiye hoşgeldiniz.
