<?php

namespace App;

use App\utils\CodigoLeyendas;
use App\utils\CodigoPrecio;
use App\utils\CodigoTributo;
use App\utils\TipoMoneda;
use DOMXPath;
use App\Utils\TipoDocumento;

class InvoiceDom
{
    private DOMXPath $path;

    public function __construct(DOMXPath $path)
    {
        $this->path = $path;
    }

    public function parseDocument(): array
    {
        $nodeNroSerie = $this->path->query('/xt:Invoice/cbc:ID');
        $serieNro = $nodeNroSerie->item(0)->nodeValue;

        $nodeFechaEmision = $this->path->query('/xt:Invoice/cbc:IssueDate');
        $fechaEmision = $nodeFechaEmision->item(0)->nodeValue;

        $nodeHoraEmision = $this->path->query('/xt:Invoice/cbc:IssueTime');
        $horaEmision = $nodeHoraEmision->item(0)->nodeValue;

        $nodeTipoDocumento = $this->path->query('/xt:Invoice/cbc:InvoiceTypeCode');
        $tipoDocumento = $nodeTipoDocumento->item(0)->nodeValue;

        $nodeTipoMoneda = $this->path->query('/xt:Invoice/cbc:DocumentCurrencyCode');
        $tipoMoneda = $nodeTipoMoneda->item(0)->nodeValue;

        $nodeFechaVencimiento = $this->path->query('/xt:Invoice/cbc:DueDate');
        $fechaVencimiento = $nodeFechaVencimiento->item(0)->nodeValue ?? '';



        $nodeNroRuc = $this->path->query('/xt:Invoice/cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID');
        $nroRuc = $nodeNroRuc->item(0)->nodeValue;

        $nodeNombreComercial = $this->path->query('/xt:Invoice/cac:AccountingSupplierParty/cac:Party/cac:PartyName/cbc:Name');
        $nombreComercial = $nodeNombreComercial->item(0)->nodeValue;

        $nodeRazonSocial = $this->path->query('/xt:Invoice/cac:AccountingSupplierParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName');
        $razonSocial = $nodeRazonSocial->item(0)->nodeValue;

        $nodeDomicilioFiscal = $this->path->query('/xt:Invoice/cac:AccountingSupplierParty/cac:Party/cac:PartyLegalEntity/cac:RegistrationAddress/cac:AddressLine/cbc:Line');
        $domicilioFiscal = $nodeDomicilioFiscal->item(0)->nodeValue;

        $nodeDireccionEntrega = $this->path->query('/xt:Invoice/cac:Delivery/cac:DeliveryLocation/cac:Address/cac:AddressLine/cbc:Line');
        $direccionEntrega = $nodeDireccionEntrega->item(0)->nodeValue ?? '';

        $nodeCodigoPais = $this->path->query('/xt:Invoice/cac:Delivery/cac:DeliveryLocation/cac:Address/cac:Country/cbc:IdentificationCode');
        $codigoPais = $nodeCodigoPais->item(0)->nodeValue ?? '';

        $nodeCodigoAsignadoXSunat = $this->path->query('/xt:Invoice/cac:AccountingSupplierParty/cac:Party/cac:PartyLegalEntity/cac:RegistrationAddress/cbc:AddressTypeCode');
        $codigoAsignadoXSunat = $nodeCodigoAsignadoXSunat->item(0)->nodeValue;



        $nodeNumeroDocAquiriente = $this->path->query('/xt:Invoice/cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID');
        $numeroDocAquiriente = $nodeNumeroDocAquiriente->item(0)->nodeValue;

        $nodeTipoDocAquiriente = $this->path->query('/xt:Invoice/cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID');
        $tipoDocAquiriente = $nodeTipoDocAquiriente->item(0)->nodeValue;

        $nodeRazonSocialAquiriente = $this->path->query('/xt:Invoice/cac:AccountingCustomerParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName');
        $razonSocialAquiriente = $nodeRazonSocialAquiriente->item(0)->nodeValue;

        $nodeDireccionAquiriente = $this->path->query('/xt:Invoice/cac:AccountingCustomerParty/cac:Party/cac:PartyLegalEntity/cac:RegistrationAddress/cac:AddressLine/cbc:Line');
        $direccionAquiriente = $nodeDireccionAquiriente->item(0)->nodeValue ?? ''   ;



        $nodeNroDocGuiaRemision = $this->path->query('/xt:Invoice/cac:DespatchDocumentReference/cbc:ID');
        $nroDocGuiaRemision = $nodeNroDocGuiaRemision->item(0)->nodeValue ?? '';

        $nodeTipoDocGuiaRemision = $this->path->query('/xt:Invoice/cac:DespatchDocumentReference/cbc:DocumentTypeCode');
        $tipoDocGuiaRemision = $nodeTipoDocGuiaRemision->item(  0)->nodeValue ?? '';




        $item = $this->path->query('/xt:Invoice/cac:InvoiceLine');
        $detalle = [];
        for ($i = 0; $i < $item->count(); $i++) {
            $pathItem = $item->item($i)->getNodePath();
            $nodeNroItem = $this->path->query($pathItem . '/cbc:ID');
            $detalle[$i]['item'] = $nodeNroItem->item(0)->nodeValue;

            $nodeUnidadMedidaItem = $this->path->query($pathItem . '/cbc:InvoicedQuantity/@unitCode');
            $detalle[$i]['unidadMedida'] = $nodeUnidadMedidaItem->item(0)->nodeValue;

            $nodeCantidadItem = $this->path->query($pathItem . '/cbc:InvoicedQuantity');
            $detalle[$i]['cantidad'] = $nodeCantidadItem->item(0)->nodeValue;

            $nodeCodigoProdtem = $this->path->query($pathItem . '/cac:Item/cac:SellersItemIdentification/cbc:ID');
            $detalle[$i]['codigoProducto'] = $nodeCodigoProdtem->item(0)->nodeValue;

            $nodeDescripcionItem = $this->path->query($pathItem . '/cac:Item/cbc:Description');
            $detalle[$i]['descripcion'] = $nodeDescripcionItem->item(0)->nodeValue;

            $nodeValorUnitItem = $this->path->query($pathItem . '/cac:Price/cbc:PriceAmount');
            $detalle[$i]['valorUnitario'] = $nodeValorUnitItem->item(0)->nodeValue;

            $nodePrecioVentaItem = $this->path->query($pathItem . '/cac:PricingReference/cac:AlternativeConditionPrice/cbc:PriceAmount');
            $detalle[$i]['precioVenta'] = $nodePrecioVentaItem->item(0)->nodeValue;

            $nodeCodigoPrecioItem = $this->path->query($pathItem . '/cac:PricingReference/cac:AlternativeConditionPrice/cbc:PriceTypeCode');
            $detalle[$i]['codigoPrecio'] = CodigoPrecio::$nombreCodigoPrecio[$nodeCodigoPrecioItem->item(0)->nodeValue];

            $nodeMontoTotalTributosItem = $this->path->query($pathItem . '/cac:TaxTotal/cbc:TaxAmount');
            $detalle[$i]['montoTotalTributos'] = $nodeMontoTotalTributosItem->item(0)->nodeValue;


            $listTaxSubtotal = $this->path->query($pathItem . '/cac:TaxTotal/cac:TaxSubtotal');
            for ($sub = 0; $sub < $listTaxSubtotal->length; $sub++) {
                $pathSubTotal = $listTaxSubtotal->item($sub)->getNodePath();

                $nodeMontoImpuestoItem = $this->path->query($pathSubTotal . '/cbc:TaxableAmount');
                $detalle[$i]['subtotal'][$sub]['montoBaseTributo'] = $nodeMontoImpuestoItem->item(0)->nodeValue;

                $nodeMontoTributoItem = $this->path->query($pathSubTotal . '/cbc:TaxAmount');
                $detalle[$i]['subtotal'][$sub]['montoTributo'] = $nodeMontoTributoItem->item(0)->nodeValue;

                $nodePorcentajeTributoItem = $this->path->query($pathSubTotal . '/cac:TaxCategory/cbc:Percent');
                $detalle[$i]['subtotal'][$sub]['porcentajeTributo'] = $nodePorcentajeTributoItem->item(0)->nodeValue ?? '';

                $nodeCodigoImpuestoItem = $this->path->query($pathSubTotal . '/cac:TaxCategory/cac:TaxScheme/cbc:ID');
                $detalle[$i]['subtotal'][$sub]['codigoTributo'] =  CodigoTributo::$nombreTributo[$nodeCodigoImpuestoItem->item(0)->nodeValue];

            }


            $nodeValorVentaItem = $this->path->query($pathItem . '/cbc:LineExtensionAmount');
            $detalle[$i]['valorVenta'] = $nodeValorVentaItem->item(0)->nodeValue;

            $nodeTieneDescuentoItem = $this->path->query($pathItem . '/cac:Allowancecharge/cbc:ChargeIndicator');
            $detalle[$i]['tieneCargoDescuento'] = (bool)($nodeTieneDescuentoItem->item(0)->nodeValue ?? '');

            $nodeMontoCargoDescuentoItem = $this->path->query($pathItem . '/cac:Allowancecharge/cbc:Amount');
            $detalle[$i]['montoCargoDescuento'] = $nodeMontoCargoDescuentoItem->item(0)->nodeValue ?? '';


        }


        $nodeMontoTotalTributos = $this->path->query( '/xt:Invoice/cac:TaxTotal/cbc:TaxAmount');
        $montoTotalesTributos = $nodeMontoTotalTributos->item(0)->nodeValue ?? '';


        $listTaxSubTotales = $this->path->query('/xt:Invoice/cac:TaxTotal/cac:TaxSubtotal');
        $totalVentaOperaciones = [];
        for ($i = 0; $i < $listTaxSubTotales->count(); $i++) {
            $pathSubTotal = $listTaxSubTotales->item($i)->getNodePath();
            $nodeTotalVentaOperaciones = $this->path->query($pathSubTotal . '/cbc:TaxableAmount');
            $totalVentaOperaciones[$i]['totalValorVenta'] = $nodeTotalVentaOperaciones->item(0)->nodeValue;

            $nodeImporteTributo = $this->path->query($pathSubTotal . '/cbc:TaxAmount');
            $totalVentaOperaciones[$i]['importeTributo'] = $nodeImporteTributo->item(0)->nodeValue;

            $nodeCodigoTributo = $this->path->query($pathSubTotal . '/cac:TaxCategory/cac:TaxScheme/cbc:ID');
            $totalVentaOperaciones[$i]['nombreTributo'] = $nodeCodigoTributo->item(0)->nodeValue ? CodigoTributo::$nombreTributo[$nodeCodigoTributo->item(0)->nodeValue] : '';
        }

        $nodeTotalValorVenta = $this->path->query( '/xt:Invoice/cac:LegalMonetaryTotal/cbc:LineExtensionAmount');
        $totalValorVenta = $nodeTotalValorVenta->item(0)->nodeValue ?? '';

        $nodeTotalPrecioVenta = $this->path->query( '/xt:Invoice/cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount');
        $totalPrecioVenta = $nodeTotalPrecioVenta->item(0)->nodeValue ?? '';

        $nodeLeyenda = $this->path->query( '/xt:Invoice/cbc:Note/@languageLocaleID');
        $leyenda = $nodeLeyenda->item(0)->nodeValue ?? '';
        $leyenda = array_key_exists($leyenda, CodigoLeyendas::$nombreLeyendas) ? CodigoLeyendas::$nombreLeyendas[$leyenda] : '';

        $nodeDescLeyenda = $this->path->query( '/xt:Invoice/cbc:Note');
        $descLeyenda = $nodeDescLeyenda->item(0)->nodeValue ?? '';





        return [
            'datosFactura' => [
                'serieNro' => $serieNro,
                'fechaEmision' => $fechaEmision,
                'horaEmision' => $horaEmision,
                'tipoDocumento' => TipoDocumento::$nombreDocumento[$tipoDocumento],
                'tipoMoneda' => TipoMoneda::$nombreMoneda[$tipoMoneda],
                'fechaVencimiento' => $fechaVencimiento,
            ],
            'datosEmisor' => [
                'numeroRuc' => $nroRuc,
                'nombreComercial' => $nombreComercial,
                'razonSocial' => $razonSocial,
                'domicilioFiscal' => $domicilioFiscal,
                'direccionEntrega' => $direccionEntrega,
                'codigoPais' => $codigoPais,
                'codigoAsignadoXSunat' => $codigoAsignadoXSunat,
            ],
            'datosAdqueriente' => [
                'numeroDocAquiriente' => $numeroDocAquiriente,
                'tipoDocAquiriente' => $tipoDocAquiriente,
                'razonSocialAquiriente' => $razonSocialAquiriente,
                'direccionAquiriente' => $direccionAquiriente,
            ],
            'infoAdicionalDocRelacionados' => [
                'nroDocGuiaRemision' => $nroDocGuiaRemision,
                'tipoDocGuiaRemision' => array_key_exists($tipoDocGuiaRemision, TipoDocumento::$nombreDocumento) ? TipoDocumento::$nombreDocumento[$tipoDocGuiaRemision] : '',
            ],
            'detalle' => $detalle,
            'totalesFactura' => [
                'montoTotalesTributos' => $montoTotalesTributos,
                'totalVentaOperaciones' => $totalVentaOperaciones,
            ],
            'totalValorVenta' => $totalValorVenta,
            'totalPrecioVenta' => $totalPrecioVenta,
            'informacionAdicional' => [
                'nombreLeyenda' => $leyenda,
                'descripcionLeyenda' => $descLeyenda,
             ],

        ];
    }
}