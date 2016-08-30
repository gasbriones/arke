/**
 *
 * Realex payment plugin
 *
 * @author Valérie Isaksen
 * @version $Id$
 * @package VirtueMart
 * @subpackage payment
 * Copyright (C) 2004-2015 Virtuemart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */

jQuery().ready(function ($) {

    /************/
    /* Handlers */
    /************/

    handleRecurringDate = function () {
        var recurring_number = $('#paramsrecurring_number').val();
        var integration = $('#paramsintegration').val();

        $('.recurring_date').parents('tr').hide();

        if (integration=='recurring') {
            if (recurring_number=='2') {
                $('.recurring_date').parents('tr').show();
            }
        }
    }

    handleIntegration = function () {
        var integration = $('#paramsintegration').val();

        $('.integration ').parents('tr').hide();

        if (integration == 'recurring') {
            $('.recurring').parents('tr').show();
        } else if(integration == 'subscribe') {
            $('.subscribe').parents('tr').show();
        }
    }

    handleSubscribeNumber = function () {
        var subscribe_number = $('#paramssubscribe_number').val();
        var integration = $('#paramsintegration').val();

        $('.subscribe_number_1 ').parents('tr').hide();
        $('.subscribe_number_2 ').parents('tr').hide();
        $('.subscribe_number_3 ').parents('tr').hide();
        if(integration == 'subscribe') {
                $('.subscribe_number_1').parents('tr').show();
            if(subscribe_number == '2') {
                $('.subscribe_number_2').parents('tr').show();
            }else if(subscribe_number == '3') {
                $('.subscribe_number_2').parents('tr').show();
                $('.subscribe_number_3').parents('tr').show();
            }
        }
    }
    handleShopMode = function () {
        var shop_mode = $('#paramsshop_mode').val();

        $('.shop_mode ').parents('tr').hide();

        if (shop_mode == 'test') {
            $('.shop_mode').parents('tr').show();
        }
    }

    /**********/
    /* Events */
    /**********/


    $('#paramsrecurring_number').change(function () {
        //handleRecurringDate();

    });
    $('#paramsshop_mode').change(function () {
        handleShopMode();

    });
    $('#paramsintegration').change(function () {
        handleIntegration();
        handleRecurringDate();
        handleSubscribeNumber();


    });
    $('#paramssubscribe_number').change(function () {
        handleSubscribeNumber();
    });
    /*****************/
    /* Initial calls */
    /*****************/
    handleShopMode();
    handleIntegration();
   // handleRecurringDate();
    handleSubscribeNumber();
});
