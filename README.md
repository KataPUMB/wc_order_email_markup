# Order Email Markup for Woocommerce
* Contributors: juanmartinezalonso
* Donate link: https://www.paypal.com/donate/?hosted_button_id=QTSH9JJGZFMCL
* Tags: Schema-org, Schema, Email Markup, Woocommerce extension, Woocommerce email markup, Woocommerce Email
* Requires at least: 4.7
* Tested up to: 5.4
* Stable tag: 4.3
* Requires PHP: 7.0
* License: GPLv2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html

## Descritpion

Automatcially add email markup schema.org to your order emails. No configuration needed, completely plug and play.

This will allow your users to have a full google experience when buying in your shop and highlight the important aspects of your order as depicted in the documentation: https://developers.google.com/gmail/markup/highlights

If you want to know more about schema.org markup for emails please visit: https://developers.google.com/gmail/markup

## Register in Google

To start using schema-org in your emails first you need to register your organisation in google.

To register please fill the following form and follow the instructions: https://docs.google.com/forms/d/e/1FAIpQLSfT5F1VJXtBjGw2mLxY2aX557ctPTsCrJpURiKJjYeVrugHBQ/viewform?pli=1&pli=1

The schema you need to send to schema.whitelisting+sample@gmail.com is the following:

```html
	<script type="application/ld+json">
    {
      "@context": "http://schema.org",
      "@type": "Order",
      "merchant": {
      "@type": "Organization",
      "name": "MY ORGANIZATION NAME"
      },
      "orderNumber": "4557",
      "orderStatus": "http://schema.org/OrderProcessing",
      "priceCurrency": "EUR",
      "price": "17.00",
      "priceSpecification": {
      "@type": "PriceSpecification",
      "price": "17.00"
      },
      "acceptedOffer": [
        {
			"@type": "Offer",
			"itemOffered": {
				"@type": "Product",
				"name": "T-SHIRT",
				"sku": "B3-1-2-1-1-1-1-2",
				"url": "https://algodejaime.com/producto/camiseta-cebra-manga-larga?attribute_pa_size=3D4-5-anos",
				"image": "https://algodejaime.com/wp-content/uploads/2021/08/algo-de-jaime-82.jpg"
			},
			"price": "20.661157",
			"priceCurrency": "EUR",
			"eligibleQuantity": {
				"@type": "QuantitativeValue",
				"value": "1"
			},
			"seller": {
				"@type": "Organization",
				"name": "MY ORGANIZATION"
			}
        }
      ],
      "url": "https://algodejaime.com/mi-cuenta/ver-pedido/28117",
      "potentialAction": {
		  "@type": "ViewAction",
		  "url": "https://algodejaime.com/mi-cuenta/ver-pedido/28117"
      },
      "orderDate": "2021-03-05T10:57:11+01:00",
      "customer": {
		  "@type": "Person",
		  "name": "CLIENT-NAME"
      },
      "billingAddress": {
		  "@type": "PostalAddress",
		  "name": "CLIENT-NAME",
		  "streetAddress": "THE STREET, 38 2ºA",
		  "addressLocality": "Madrid",
		  "addressRegion": "M",
		  "addressCountry": "ES"
      }
    }
	</script>
```
    
Remember you should change the values for actual values for your shop.

If you want to know more about schema.org markup for emails please visit: https://developers.google.com/gmail/markup

## Changelog

### 1.0
* First Release