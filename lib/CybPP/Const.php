<?php

/*
 * This file is part of the phpCyberPlusPaiement lib.
 *
 * (c) Pierre Tachoire <pierre.tachoire@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * CybPP permet de gérer un paiment en ligne
 * via la technologie de Cyberplus Paiement
 *
 */
class CybPP_Const {
	
	const QUERY_URL = 'https://systempay.cyberpluspaiement.com/vads-payment/';
	
	/**
	 * Information complémentaire facultative destinée à indiquer le nom de la
	 * contribution utilisée lors du paiement (joomla, oscommerce...).
	 * maxlen 255
	 * @var string
	 */
	const DEFAULT_CONTRIB = null;
	
	/**
	 * Paramètre obligatoire indiquant la monnaie à utiliser, selon la norme ISO
	 * 4217 (code numérique).
	 * @see http://www.iso.org/iso/support/currency_codes_list-1.htm
	 * Pour l’Euro, la valeur est 978.
	 * len 3
	 * @var number
	 */
	const DEFAULT_CURRENCY = 978;
	
	/**
	 * Code pays du client à la norme ISO 3166. Paramètre optionnel.
	 * @see http://www.iso.org/iso/english_country_names_and_code_elements
	 * Pour la France, le code est FR.
	 * 
	 * len 2
	 * @var string
	 */
	const DEFAULT_CUST_COUNTRY = 'FR';
	
	/**
	 * Paramètre obligatoire indiquant le mode de sollicitation de la plateforme
	 * de paiement :
	 * TEST : utilisation du mode test, nécessite d’employer le certificat de test
	 * pour la signature.
	 * PRODUCTION : utilisation du mode production, nécessite d’employer le
	 * certificat de production pour la signature.
	 * @var string
	 */
	const CTX_MODE_TEST = 'TEST';
	const CTX_MODE_PRODUCTION = 'PRODUCTION';
	const DEFAULT_CTX_MODE = self::CTX_MODE_TEST;
	
	/**
	 * Paramètre optionnel indiquant la langue de la page de paiement
	 * (norme ISO 639-1).
	 * len 2
	 * @var string
	 */
	const DEFAULT_LANGUAGE = 'fr';
	
	/**
	 * Ce paramètre obligatoire contient la liste des types de cartes disponibles pour ce
	 * site, séparés par des " ;". Si la liste ne contient qu'un type de carte, la page de
	 * saisie des données du paiement sera directement présentée. Sinon la page de
	 * sélection du moyen de paiement sera présentée. Si ce paramètre est vide alors
	 * l’ensemble des moyens de paiement défini dans l’outil de gestion de caisse sera
	 * présenté en sélection. Par défaut la valeur VIDE est conseillée.
	 * Les différents types de carte possibles sont :
	 *               Réseau de la carte                 Valorisation ‘payment_cards’
	 *               Amex                                    AMEX
	 *               CB                                      CB
	 *               Eurocard / MasterCard                       MASTERCARD
	 *               Visa                                    VISA
	 *               Maestro                                MAESTRO
	 *               e-carte bleue                         E-CARTEBLEUE
	 * maxlen 127
	 * @var string
	 */
	const PAYMENT_CARDS_AMEX = 'AMEX';
	const PAYMENT_CARDS_CB = 'CB';
	const PAYMENT_CARDS_MASTERCARD = 'MASTERCARD';
	const PAYMENT_CARDS_VISA = 'VISA';
	const PAYMENT_CARDS_MAESTRO = 'MAESTRO';
	const PAYMENT_CARDS_E_CARTE_BLEUE = 'E-CARTEBLEUE';
	const PAYMENT_CARDS_SEPARATOR = ';';
	
	/**
	 * Ce paramètre obligatoire indique le type du paiement :
	 * - SINGLE indique un paiement unitaire.
	 * - MULTI indique un paiement en plusieurs fois. Dans ce cas, le paramètre est
	 *    constitué de la chaîne « MULTI: », suivi par des paires clés/valeurs séparées par
	 *    des « ; ». Les paramètres sont les suivants :
	 *           o « first » indique le montant du premier paiement.
	 *           o « count » indique le nombre de paiements total.
	 *           o « period » indique l’intervalle en nombre de jours entre 2 paiements.
	 *    Exemple :
	 *       currency=978
	 *       amount=10000
	 *       payment_config=MULTI:first=5000;count=3;period=30
	 *    Dans cette configuration :
	 *    - Un premier paiement de 50 euros sera effectué à aujourd’hui
	 *    + « capture_delay » jours.
	 *    - Un deuxième paiement de 25 euros sera effectué à aujourd’hui + 
	 *    + «capture_delay » + 30 jours.
	 *    - Un troisième et dernier paiement de 25 euros sera effectué à aujourd’hui
	 *    + « capture_delay » + 60 jours.
	 *    Remarque : si la date de validité de la carte ne permet pas de réaliser le
	 *    dernier paiement, la demande sera refusée par la plateforme.
	 *    
	 *    EDIT 05/05/2010 : payment config peut être a null dans le cas d'une validation
	 *    via le back office
	 *    
	 * @var string
	 */
	const PAYMENT_CONFIG_SINGLE = 'SINGLE';
	const PAYMENT_CONFIG_MULTI = 'MULTI';
	const PAYMENT_CONFIG_NULL = null;
	const DEFAULT_PAYMENT_CONFIG = self::PAYMENT_CONFIG_SINGLE;
	
	
	/**
	 *     Paramètre facultatif définissant la source du paiement :
	 *     - Paramètre non défini ou valeur vide, indique un paiement de type
	 *        eCommerce. Dans ce cas, la garantie de paiement est calculée
	 *        conformément aux options du commerce concerné.
	 *     - BO indique un paiement effectué depuis le « Back Office » (saisie manuelle),
	 *        dans ce cas il n’y a pas de garantie de paiement.
	 *     - MOTO indique un paiement effectué par un opérateur suite à une
	 *        commande par téléphone ou eMail (Mail Or Telephone Order).
	 *     - CC indique un paiement effectué via un centre d’appel (Call Center).
	 *     - OTHER indique un paiement effectué par toute autre source que celles
	 *        précédemment définies.
	 *     Des informations complémentaires sur l’origine du paiement peuvent être
	 *     définies dans le paramètre user_info.
	 *     NB : L’utilisation de ce paramétrage n’est permise que pour les commerçants
	 *     ayant souscrit une offre adéquate. Merci de contacter votre chargé de
	 *     clientèle bancaire pour plus d’informations.
	 *     
	 * maxlen 5
	 * @var string
	 */
	const PAYMENT_SRC_ECOMMERCE = null;
	const PAYMENT_SRC_BO = 'BO';
	const PAYMENT_SRC_MOTO = 'MOTO';
	const PAYMENT_SRC_CC = 'CC';
	const PAYMENT_SRC_OTHER = 'OTHER';
	const DEFAULT_PAYMENT_SRC = self::PAYMENT_SRC_ECOMMERCE;

	/**
	 * Paramètre obligatoire permettant à la plateforme de vérifier la validité de la
	 * requête transmise (voir le chapitre suivant).
	 * @var string
	 */
	const SIGNATURE_SEPARATOR = '+';
	
	/**
	 * Ce paramètre est obligatoire. Correspondre à la date locale du site marchand
	 * au format AAAAMMJJHHMMSS.
	 * len 14
	 * @var number
	 */
	const TRANS_DATE_FORMAT = 'Ymdhis';	
	
	/**
	 * Paramètre obligatoire indiquant si cette transaction devra faire l'objet d'une
	 * validation manuelle de la part du commerçant. Si ce paramètre est vide alors la
	 * configuration par défaut du site sera prise. Cette dernière est paramétrable dans
	 * l’outil de gestion de caisse Cyberplus Paiement par toutes les personnes dûment
	 * habilitées.
	 * len 1
	 * @var number
	 */
	const VALIDATION_MODE_AUTO = 0;
	const VALIDATION_MODE_MANUEL = 1;
	const DEFAULT_VALIDATION_MODE = self::VALIDATION_MODE_AUTO;

	/**
	 * Paramètre obligatoire. La version actuelle est V1.
	 * @var string
	 */
	const VERSION_V1 = 'V1';
	const DEFAULT_VERSION = self::VERSION_V1;

	/**
	 *   Si l’autorisation a été réalisée avec succès, indique la garantie du paiement, liée
	 *     à 3D-Secure :
	 *           YES          Le paiement est garanti
	 *           NO          Le paiement n’est pas garanti
	 *           UNKNOWN          Suite à une erreur technique, le paiement ne peut pas être garanti
	 *           Non valorisé     Garantie de paiement non applicable
	 * @var string
	 */
	const WARRANTY_RESULT_YES = 'YES';
	const WARRANTY_RESULT_NO = 'NO';
	const WARRANTY_RESULT_UNKNOWN = 'UNKNOWN';
	const WARRANTY_RESULT_NAN = null;
	
	/**
	 * Indique comment a été réalisée la demande d’autorisation. Ce champ peut
	 * prendre les valeurs suivantes :
	 * - FULL : correspond à une autorisation du montant total de la transaction dans
	 *     le cas d’un paiement unitaire avec remise à moins de 6 jours, ou à une
	 *     autorisation du montant du premier paiement dans le cas du paiement en N
	 *     fois, dans le cas d’une remise de ce premier paiement à moins de 6 jours.
	 * - MARK : correspond à une prise d’empreinte de la carte, dans le cas ou le
	 *     paiement est envoyé en banque à plus de 6 jours.
	 * @var string
	 */
	const AUTH_MODE_FULL = 'FULL';
	const AUTH_MODE_MARK = 'MARK';
	
	/**
	 * result
	 * Code retour général. Est l'une des valeurs suivantes :
	 * - 00 : Paiement réalisé avec succès.
	 * - 02 : Le commerçant doit contacter la banque du porteur.
	 * - 05 : Paiement refusé.
	 * - 17 : Annulation client.
	 * - 30 : Erreur de format de la requête. A mettre en rapport avec la valorisation
	 *    du champ extra_result.
	 * - 96 : Erreur technique lors du paiement.
	 * len 2
	 * @var string
	 */
	const RESULT_SUCCESS = '00';
	const RESULT_CONTACT_BANQ = '02';
	const RESULT_PAYMENT_REFUSED = '05';
	const RESULT_CUST_CANCEL = '17';
	const RESULT_QUERY_ERROR = '30';
	const RESULT_TECHNICAL_ERROR = '96';
	
}