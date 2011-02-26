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
class CybPP_Query {
	
	/**
	 * certificat du site
	 * @var string
	 */
	protected $certificat = null;
	
	/**
	 * Paramètre obligatoire. Montant de la transaction exprimé en son unité indivisible 
	 * (exemple : en cents pour l'Euro).
	 * maxlen 12
	 * @var number
	 */
	protected $amount = null;
	
	/**
	 * Paramètre obligatoire indiquant le délai en nombre de jours avant remise en
	 * banque. Si ce paramètre est vide (il doit néanmoins être transmis), alors la valeur
	 * par défaut sera utilisée. Cette dernière est paramétrable dans l’outil de gestion
	 * de caisse Cyberplus Paiement par toutes les personnes dûment habilitées.
	 * maxlen 3
	 * @var number
	 */
	protected $capture_delay = null;
	
	/**
	 * Information complémentaire facultative destinée à indiquer le nom de la
	 * contribution utilisée lors du paiement (joomla, oscommerce...).
	 * maxlen 255
	 * @var string
	 */

	protected $contrib = CybPP_Const::DEFAULT_CONTRIB;
	

	/**
	 * Paramètre obligatoire indiquant la monnaie à utiliser, selon la norme ISO
	 * 4217 (code numérique).
	 * @see http://www.iso.org/iso/support/currency_codes_list-1.htm
	 * Pour l’Euro, la valeur est 978.
	 * len 3
	 * @var number
	 */
	protected $currency = CybPP_Const::DEFAULT_CURRENCY;
	
	/**
	 * Adresse e-mail du client, nécessaire pour lui envoyer un mail récapitulatif de la
	 * transaction. Paramètre optionnel.
	 * maxlen 255
	 * @var string
	 */
	protected $cust_email = null;
	
	/**
	 * Paramètre facultatif correspondant à un identifiant client pour le marchand.
	 * maxlen 63
	 * @var string
	 */
	protected $cust_id = null;
	
	
	
	/**
	 * Paramètres optionnels concernant le client, et correspondant respectivement à :
	 *    • cust_name : nom du client
	 *    • cust_title : civilité du client
	 *    • cust_address : adresse du client
	 *    • cust_zip : code postal du client
	 *    • cust_city : vile du client
	 *    • cust_phone : numéro de téléphone du client
	 */
	
	
	/**
	 * maxlen 127
	 * @var string
	 */
	protected $cust_name = null;
	
	/**
	 * maxlen 63
	 * @var string
	 */
	protected $cust_title = null;
	
	/**
	 * maxlen 255
	 * @var string
	 */
	protected $cust_address = null;
	
	/**
	 * maxlen 63
	 * @var string
	 */
	protected $cust_zip = null;
	
	/**
	 * maxlen 63
	 * @var string
	 */
	protected $cust_city = null;
	
	/**
	 * maxlen 63
	 * @var string
	 */
	protected $cust_phone = null;
	
	/**
	 * Code pays du client à la norme ISO 3166. Paramètre optionnel.
	 * @see http://www.iso.org/iso/english_country_names_and_code_elements
	 * Pour la France, le code est FR.
	 * 
	 * len 2
	 * @var string
	 */
	protected $cust_country = CybPP_Const::DEFAULT_CUST_COUNTRY;
	
	
	/**
	 * Paramètre obligatoire indiquant le mode de sollicitation de la plateforme
	 * de paiement :
	 * TEST : utilisation du mode test, nécessite d’employer le certificat de test
	 * pour la signature.
	 * PRODUCTION : utilisation du mode production, nécessite d’employer le
	 * certificat de production pour la signature.
	 * @var string
	 */
	protected $ctx_mode = CybPP_Const::DEFAULT_CTX_MODE;
	
	/**
	 * Paramètre optionnel indiquant la langue de la page de paiement
	 * (norme ISO 639-1).
	 * len 2
	 * @var string
	 */
	protected $language = CybPP_Const::DEFAULT_LANGUAGE;
	
	/**
	 * Ce paramètre est optionnel. Il correspond à un numéro de commande qui
	 * pourra être rappelé dans l'e-mail adressé au client. Sa taille maximale est de 12
	 * caractères alphanumériques.
	 * maxlen 12
	 * @var string
	 */
	protected $order_id = null;
	
	/**
	 * Ces paramètres optionnels sont des champs libres. Ils peuvent par exemple servir
	 * à stocker un résumé de la commande.
	 * maxlen 255
	 * @var string
	 */
	protected $order_info = null;
	protected $order_info2 = null;
	protected $order_info3 = null;
	
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
	protected $payment_cards = null;
	
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
	 * @var string
	 */
	protected $payment_config = CybPP_Const::DEFAULT_PAYMENT_CONFIG;
	
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
	protected $payment_src = CybPP_Const::DEFAULT_PAYMENT_SRC;
	
	
	/**
	 * Paramètre obligatoire permettant à la plateforme de vérifier la validité de la
	 * requête transmise (voir le chapitre suivant).
	 * @var string
	 */
	protected $signature = null;
	
	/**
	 * Paramètre obligatoire attribué lors de l'inscription à la plateforme de paiement.
	 * Sa valeur est consultable sur l’interface de l’outil de gestion de caisse Cyberplus
	 * Paiement dans l’onglet « Paramétrages » / « Boutique » par toutes les personnes
	 * habilitées.
	 * len 8
	 * @var number 
	 */
	protected $site_id = null;
	
	/**
	 * Paramètre facultatif permettant de personnaliser certains paramètres de la page
	 * de paiement standard, comme les logos, bandeaux et certains messages.
	 * Contacter le support technique (supportvad@lyra-network.com) pour plus
	 * d’informations.
	 * maxlen 255
	 * @var string
	 */
	protected $theme_config = null;
	
	/**
	 * Ce paramètre est obligatoire. Correspondre à la date locale du site marchand
	 * au format AAAAMMJJHHMMSS.
	 * len 14
	 * @var number
	 */
	protected $trans_date = null;
	
	/**
	 * Ce paramètre est obligatoire. Il est constitué de 6 caractères numériques et doit
	 * être unique pour chaque transaction sur un site donné sur la journée. En effet
	 * l'identifiant unique de transaction au niveau de la plateforme de paiement est
	 * constitué du site_id, de trans_date restreint à la valeur de la journée (partie
	 * correspondant à AAAAMMJJ) et de trans_id. Il est à la charge du site marchand
	 * de garantir cette unicité sur la journée. Il doit être impérativement compris entre
	 * 000000 et 899999. La tranche 900000 et 999999 est interdite.
	 * len 6
	 * @var number
	 */
	protected $trans_id = null;
	
	/**
	 * Paramètre obligatoire indiquant si cette transaction devra faire l'objet d'une
	 * validation manuelle de la part du commerçant. Si ce paramètre est vide alors la
	 * configuration par défaut du site sera prise. Cette dernière est paramétrable dans
	 * l’outil de gestion de caisse Cyberplus Paiement par toutes les personnes dûment
	 * habilitées.
	 * len 1
	 * @var number
	 */
	protected $validation_mode = null;
	
	/**
	 * Paramètre obligatoire. La version actuelle est V1.
	 * @var string
	 */
	protected $version = CybPP_Const::DEFAULT_VERSION;
	
	/**
	 * URL facultative où sera redirigé le client en cas de succès du paiement, après
	 * appui du bouton " retourner à la boutique ". Seuls les ports http (port 80) et https
	 * port 443 sont possibles.
	 * maxlen 127
	 * @var string
	 */
	protected $url_success = null;
	
	/**
	 * URL facultative où sera redirigé le client en cas de refus d’autorisation avec le
	 * code 02 « referral », après appui du bouton " retourner à la boutique ". Seuls les
	 * ports http (port 80) et https port 443 sont possibles.
	 * maxlen 127
	 * @var string
	 */
	protected $url_referral = null;
	
	/**
	 * URL facultative où sera redirigé le client en cas de refus pour toute autre cause
	 * que le " referral ", après appui du bouton " retourner à la boutique ". Seuls les ports
	 * http (port 80) et https port 443 sont possibles.
	 * maxlen 127
	 * @var string
	 */
	protected $url_refused = null;
	
	/**
	 * URL facultative où sera redirigé le client si celui-ci appuie sur " annuler et
	 * retourner à la boutique " avant d'avoir procédé au paiement. Seuls les ports http
	 * (port 80) et https port 443 sont possibles.
	 * maxlen 127
	 * @var string
	 */
	protected $url_cancel = null;
	
	/**
	 * URL facultative où sera redirigé le client en cas d'erreur de traitement interne.
	 * Seuls les ports http (port 80) et https port 443 sont possibles.
	 * maxlen 127
	 * @var string
	 */
	protected $url_error = null;
	
	/**
	 * URL facultative où sera redirigé par défaut le client après un appui sur le bouton 
	 * "retourner à la boutique ", si les URL correspondantes aux cas de figure vus
	 * précédemment ne sont pas renseignées. Seuls les ports http (port 80) et https port
	 * 443 sont possibles.
	 * Si cette URL n’est pas présente dans la requête, alors c’est la configuration dans
	 * l’outil de gestion de caisse qui sera prise en compte.
	 * En effet il est possible de configurer des URL de retour, en mode TEST et en mode
	 * PRODUCTION. Ces paramètres sont nommés « URL de retour de la boutique » et
	 * « URL de retour de la boutique en mode test » respectivement, et sont accessibles
	 * dans l’onglet « Configuration » lors du paramétrage d’une boutique.
	 * Si toutefois aucune URL n’est présente, que ce soit dans la requête ou dans le
	 * paramétrage de la boutique, alors le bouton « retourner à la boutique »
	 * redirigera vers l’URL générique de la boutique (paramètre nommé « URL » dans la
	 * configuration de la boutique).
	 * maxlen 127
	 * @var string
	 */
	protected $url_return = null;
	
	/**
	 * Paramètre facultatif spécifiant des informations complémentaires quant au
	 * paiement. Dans le cas d’un paiement via une saisie manuelle, ce paramètre
	 * contient l’identifiant de l’utilisateur à l’origine de la transaction. Dans les autres
	 * cas de paiement (eMail, téléphone...) tels que définis par le paramètre
	 * payment_src, ce paramètre doit servir à identifier l’opérateur à l’origine de la
	 * transaction.
	 * maxlen 255
	 * @var string
	 */
	protected $user_info = null;
	
	/**
	 * Le constructeur par défaut se contente
	 * de définir le transdate
	 * @return unknown_type
	 */
	public function __construct() {
		$this->setTransDate( date( CybPP_Const::TRANS_DATE_FORMAT ) );
	}
	
	/**
	 * certificat du site
	 * @param string $value
	 * @return void
	 */
	public function setCertificat(  $value ) {
		$value = strval( $value );
		
		if( strlen( $value ) != 16 ) {
			throw new CybPP_Exception( 'Certificat has bad length', -2 );	
		}
		
		$this->certificat = $value;
	}
	
	/**
	 * Paramètre obligatoire. Montant de la transaction exprimé en son unité indivisible
	 * (exemple : en cents pour l'Euro).
	 * @param int $value
	 * @return void
	 */
	public function setAmount( $value ) {
		
		$value = intval( $value );
		
		if( strlen( strval( $value )) > 12 ) {
			throw new CybPP_Exception( 'Amount is to long', 9 );	
		}
		
		if( $value == null ) {
			throw new CybPP_Exception( 'Amount is mandatory', 9 );
		}
		
		$this->amount = $value;
	}
	
	/**
	 * Paramètre obligatoire. Montant de la transaction exprimé en son unité indivisible
	 * (exemple : en cents pour l'Euro).
	 * @return int
	 */
	public function getAmount() {
		return $this->amount;
	}
	
	/**
	 * Paramètre obligatoire indiquant le délai en nombre de jours avant remise en
	 * banque. Si ce paramètre est vide (il doit néanmoins être transmis), alors la valeur
	 * par défaut sera utilisée. Cette dernière est paramétrable dans l’outil de gestion
	 * de caisse Cyberplus Paiement par toutes les personnes dûment habilitées.
	 * @param int $value
	 * @return void
	 */
	public function setCaptureDelay( $value ) {
		
		$value = intval( $value );
		
		if( strlen( strval( $value )) > 3 ) {
			throw new CybPP_Exception( 'Capture Delay is to long', 6 );	
		}
		
		if( $value == null ) {
			throw new CybPP_Exception( 'Capture Delay is mandatory', 6 );
		}
		
		$this->capture_delay = $value;
	}
	
	/**
	 * Paramètre obligatoire indiquant le délai en nombre de jours avant remise en
	 * banque. Si ce paramètre est vide (il doit néanmoins être transmis), alors la valeur
	 * par défaut sera utilisée. Cette dernière est paramétrable dans l’outil de gestion
	 * de caisse Cyberplus Paiement par toutes les personnes dûment habilitées.
	 * @return int
	 */
	public function getCaptureDelay() {
		return $this->capture_delay;	
	}
	
	/**
	 * Information complémentaire facultative destinée à indiquer le nom de la
	 * contribution utilisée lors du paiement (joomla, oscommerce...).
	 * @param string $value
	 * @return void
	 */
	public function setContrib( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 255 ) {
			throw new CybPP_Exception( 'Contrib is to long', 31 );	
		}
		
		$this->contrib = $value;
	}
	
	/**
	 * Information complémentaire facultative destinée à indiquer le nom de la
	 * contribution utilisée lors du paiement (joomla, oscommerce...).
	 * @return tring
	 */
	public function getContrib() {
		return $this->contrib;
	}
	
	/**
	 * Paramètre obligatoire indiquant la monnaie à utiliser, selon la norme ISO
	 * 4217 (code numérique).
	 * @see http://www.iso.org/iso/support/currency_codes_list-1.htm
	 * Pour l’Euro, la valeur est 978.
	 * @param int $value
	 * @return void
	 */
	public function setCurrency( $value ) {
		$value = intval( $value );
		
		if( strlen( strval( $value ) ) != 3 ) {
			throw new CybPP_Exception( 'Currency has a bad length', 10 );	
		}
		
		if( $value == null ) {
			throw new CybPP_Exception( 'Currency Delay is mandatory', 10 );
		}
		
		$this->currency = $value;
	}
	
	/**
	 * Paramètre obligatoire indiquant la monnaie à utiliser, selon la norme ISO
	 * 4217 (code numérique).
	 * @see http://www.iso.org/iso/support/currency_codes_list-1.htm
	 * Pour l’Euro, la valeur est 978.
	 * @return int
	 */
	public function getCurrency() {
		return $this->currency;
	}
	
	/**
	 * cust_address : adresse du client
	 * @param string $value
	 * @return void
	 */
	public function setCustAddress( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 255 ) {
			throw new CybPP_Exception( 'Cust Adress is to long', 19 );	
		}
		
		$this->cust_address = $value;
	}
	
	/**
	 * cust_address : adresse du client
	 * @return string
	 */
	public function getCustAddress() {
		return $this->cust_address;
	}
	
	/**
	 * Code pays du client à la norme ISO 3166. Paramètre optionnel.
	 * @see http://www.iso.org/iso/english_country_names_and_code_elements
	 * Pour la France, le code est FR.
	 * @param string $value
	 * @return void
	 */
	public function setCustCountry( $value ) {
		$value = intval( $value );
		
		if( $value != null && strlen( strval( $value ) ) != 2 ) {
			throw new CybPP_Exception( 'Cust Country has a bad length', 22 );	
		}
		
		$this->cust_country = $value;
	}
	
	/**
	 * Code pays du client à la norme ISO 3166. Paramètre optionnel.
	 * @see http://www.iso.org/iso/english_country_names_and_code_elements
	 * Pour la France, le code est FR.
	 * @return string
	 */
	public function getCustCountry() {
		return $this->cust_country;
	}
	
	/**
	 * Adresse e-mail du client, nécessaire pour lui envoyer un mail récapitulatif de la
	 * transaction. Paramètre optionnel.
	 * @param string $value
	 * @return void
	 */
	public function setCustEmail( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 127 ) {
			throw new CybPP_Exception( 'Cust Email is to long', 15 );	
		}
		
		$this->cust_email = $value;
	}
	
	/**
	 * Adresse e-mail du client, nécessaire pour lui envoyer un mail récapitulatif de la
	 * transaction. Paramètre optionnel.
	 * @return string
	 */
	public function getCustEmail() {
		return $this->cust_email;
	}
	
	/**
	 * Paramètre facultatif correspondant à un identifiant client pour le marchand. 
	 * @param string $value
	 * @return void
	 */
	public function setCustId( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 63 ) {
			throw new CybPP_Exception( 'Cust Id is to long', 16 );	
		}
		
		$this->cust_id = $value;
	}
	
	/**
	 * Paramètre facultatif correspondant à un identifiant client pour le marchand. 
	 * @return string
	 */
	public function getCustId() {
		return $this->cust_id;
	}
	
	/**
	 * cust_name : nom du client
	 * @param string $value
	 * @return void
	 */
	public function setCustName( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 127 ) {
			throw new CybPP_Exception( 'Cust Name is to long', 18 );	
		}
		
		$this->cust_name = $value;
	}
	
	/**
	 * cust_name : nom du client
	 * @return string
	 */
	public function getCustName() {
		return $this->cust_name;
	}
	
	/**
	 * cust_phone : numéro de téléphone du client
	 * @param string $value
	 * @return void
	 */
	public function setCustPhone( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 63 ) {
			throw new CybPP_Exception( 'Cust Phone is to long', 23 );	
		}
		
		$this->cust_phone = $value;
	}
	
	/**
	 * cust_phone : numéro de téléphone du client
	 * @return string
	 */
	public function getCustPhone() {
		return $this->cust_phone;
	}
	
	/**
	 * cust_title : civilité du client
	 * @param string $value
	 * @return void
	 */
	public function setCustTitle( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 63 ) {
			throw new CybPP_Exception( 'Cust Title is to long', 17 );	
		}
		
		$this->cust_title = $value;
	}
	
	
	/**
	 * cust_title : civilité du client
	 * @return string
	 */
	public function getCustTitle() {
		return $this->cust_title;
	}
	
	/**
	 * cust_city : ville du client
	 * @param string $value
	 * @return void
	 */
	public function setCustCity( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 63 ) {
			throw new CybPP_Exception( 'Cust City is to long', 21 );	
		}
		
		$this->cust_city = $value;
	}
	
	/**
	 * cust_city : ville du client
	 * @return string
	 */
	public function getCustCity() {
		return $this->cust_city;
	}
	
	/**
	 * cust_zip : code postal du client
	 * @param string $value
	 * @return void
	 */
	public function setCustZip( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 63 ) {
			throw new CybPP_Exception( 'Cust Zip is to long', 20 );	
		}
		
		$this->cust_zip = $value;
	}
	
	/**
	 * cust_zip : code postal du client
	 * @return string
	 */
	public function getCustZip() {
		return $this->cust_zip;
	}
	
	/**
	 * Paramètre obligatoire indiquant le mode de sollicitation de la plateforme
	 * de paiement :
	 * TEST : utilisation du mode test, nécessite d’employer le certificat de test
	 * pour la signature.
	 * PRODUCTION : utilisation du mode production, nécessite d’employer le
	 * certificat de production pour la signature. 
	 * @param string $value
	 * @return void
	 */
	public function setCtxMode( $value ) {
		switch( $value ) {
			case CybPP_Const::CTX_MODE_TEST :
				$this->ctx_mode = CybPP_Const::CTX_MODE_TEST;
				break;
			case CybPP_Const::CTX_MODE_PRODUCTION :
				$this->ctx_mode = CybPP_Const::CTX_MODE_PRODUCTION;
				break;
			default :
				throw new CybPP_Exception( 'Bad Ctx Mode', 11 );
		}
	}
	
	/**
	 * Paramètre obligatoire indiquant le mode de sollicitation de la plateforme
	 * de paiement :
	 * TEST : utilisation du mode test, nécessite d’employer le certificat de test
	 * pour la signature.
	 * PRODUCTION : utilisation du mode production, nécessite d’employer le
	 * certificat de production pour la signature.
	 * @return string
	 */
	public function getCtxMode() {
		return $this->ctx_mode;
	}
	
	/**
	 * Paramètre optionnel indiquant la langue de la page de paiement
	 * (norme ISO 639-1).
	 * @param string $value
	 * @return void
	 */
	public function setLanguage( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) != 2 ) {
			throw new CybPP_Exception( 'Language has bad lenght', 12 );	
		}
		
		$this->language = $value;
	}
	
	/**
	 * Paramètre optionnel indiquant la langue de la page de paiement
	 * (norme ISO 639-1).
	 * @return void
	 */
	public function getLanguage() {
		return $this->language;
	}
	
	/**
	 * Ce paramètre est optionnel. Il correspond à un numéro de commande qui
	 * pourra être rappelé dans l'e-mail adressé au client. Sa taille maximale est de 12
	 * caractères alphanumériques.
	 * @param string $value
	 * @return void
	 */
	public function setOrderId( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 12 ) {
			throw new CybPP_Exception( 'order Id is too long', 13 );	
		}
		
		$this->order_id = $value;
	}
	
	/**
	 * Ce paramètre est optionnel. Il correspond à un numéro de commande qui
	 * pourra être rappelé dans l'e-mail adressé au client. Sa taille maximale est de 12
	 * caractères alphanumériques.
	 * @return string
	 */
	public function getOrderId() {
		return $this->order_id;
	}
	
	/**
	 * Ces paramètres optionnels sont des champs libres. Ils peuvent par exemple servir
	 * à stocker un résumé de la commande.
	 * @param string $value
	 * @return void
	 */
	public function setOrderInfoAll( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 255*3 ) {
			throw new CybPP_Exception( 'order Info is too long', 14 );	
		}
		
		//je vais découper mes order info par tranche de 255...
		$order_infos = str_split( $value, 255 );
		if( isset( $order_infos[0] )) {
			$this->order_info = $order_infos[0];
			if( isset( $order_infos[1] )) {
				$this->order_info2 = $order_infos[1];
				if( isset( $order_infos[2] )) {
					$this->order_info3 = $order_infos[2];						
				} else {
					$this->order_info3 = null;						
				}				
			} else {
				$this->order_info2 = null;
				$this->order_info3 = null;	
			}
		} else {
			$this->order_info = null;
			$this->order_info2 = null;
			$this->order_info3 = null;
		}		
		
	}
	
	/**
	 * Ces paramètres optionnels sont des champs libres. Ils peuvent par exemple servir
	 * à stocker un résumé de la commande.
	 * @return string
	 */
	public function getOrderInfoAll() {
		return $this->order_info.$this->order_info2.$this->order_info3;
	}
	
	/**
	 * Ces paramètres optionnels sont des champs libres. Ils peuvent par exemple servir
	 * à stocker un résumé de la commande.
	 * @return string
	 */
	public function getOrderInfo() {
		return $this->order_info;
	}
	
	/**
	 * Ces paramètres optionnels sont des champs libres. Ils peuvent par exemple servir
	 * à stocker un résumé de la commande.
	 * @return string
	 */
	public function getOrderInfo2() {
		return $this->order_info2;
	}
	
	/**
	 * Ces paramètres optionnels sont des champs libres. Ils peuvent par exemple servir
	 * à stocker un résumé de la commande.
	 * @return void
	 */
	public function getOrderInfo3() {
		return $this->order_info3;
	}
	
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
	 * @param array $values
	 * @return void
	 */
	public function setPaymentCards( array $values ) {
		
		foreach( $values as $value ) {
			switch( $value ) {
				case CybPP_Const::PAYMENT_CARDS_AMEX :
				case CybPP_Const::PAYMENT_CARDS_CB :
				case CybPP_Const::PAYMENT_CARDS_E_CARTE_BLEUE :
				case CybPP_Const::PAYMENT_CARDS_MAESTRO :
				case CybPP_Const::PAYMENT_CARDS_MASTERCARD :
				case CybPP_Const::PAYMENT_CARDS_VISA :
					break;
				default :
					throw new CybPP_Exception( 'Payment Cards has a bad value', 8 );
					break;		
			}
		}
		
		$value = implode( CybPP_Const::PAYMENT_CARDS_SEPARATOR, $values );
		
		if( $value != null && strlen( $value ) > 127 ) {
			throw new CybPP_Exception( 'Payment Cards has a bad length', 8 );	
		}
		
		$this->payment_cards = $value;
	}
	
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
	 * @return array
	 */
	public function getPaymentCardsAsArray() {
		return explode(CybPP_Const::PAYMENT_CARDS_SEPARATOR, $this->payment_cards );
	}
	
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
	 * @return string
	 */
	public function getPaymentCards() {
		return $this->payment_cards;
	}
	
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
	 * @param string $value
	 * @return void
	 */
	public function setPaymentConfig( $value ) {
		$value = strval( $value );
		
		switch( $value ) {
			case CybPP_Const::PAYMENT_CONFIG_SINGLE :
				$this->payment_config = $value;
				break;
			case CybPP_Const::PAYMENT_CONFIG_MULTI :
				throw new CybPP_Exception( 'Payment Config MULTI not yet supported', 7 );
				break;
			default :
				throw new CybPP_Exception( 'Payment Config has a bad value', 7 );
				break;
		}
	}
	
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
	 * @return string
	 */
	public function getPaymentConfig() {
		return $this->payment_config;
	}
	
	/**
	 * Paramètre obligatoire attribué lors de l'inscription à la plateforme de paiement.
	 * Sa valeur est consultable sur l’interface de l’outil de gestion de caisse Cyberplus
	 * Paiement dans l’onglet « Paramétrages » / « Boutique » par toutes les personnes
	 * habilitées.
	 * @param int $value
	 * @return void
	 */
	public function setSiteId( $value ) {
		$value = intval( $value );
		
		if( strlen( strval( $value ) ) != 8 ) {
			throw new CybPP_Exception( 'Site id as bad length', 2 );	
		}
		
		$this->site_id = $value;
	}
	
	/**
	 * Paramètre obligatoire attribué lors de l'inscription à la plateforme de paiement.
	 * Sa valeur est consultable sur l’interface de l’outil de gestion de caisse Cyberplus
	 * Paiement dans l’onglet « Paramétrages » / « Boutique » par toutes les personnes
	 * habilitées.
	 * @return int
	 */
	public function getSiteId() {
		return $this->site_id;
	}
	
	/**
	 * Paramètre facultatif permettant de personnaliser certains paramètres de la page
	 * de paiement standard, comme les logos, bandeaux et certains messages.
	 * Contacter le support technique (supportvad@lyra-network.com) pour plus
	 * d’informations.
	 * @param string $value
	 * @return void
	 */
	public function setThemeConfig( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 255 ) {
			throw new CybPP_Exception( 'Theme Config is too long', 32 );	
		}
		
		$this->theme_config = $value;
	}
	
	/**
	 * Paramètre facultatif permettant de personnaliser certains paramètres de la page
	 * de paiement standard, comme les logos, bandeaux et certains messages.
	 * Contacter le support technique (supportvad@lyra-network.com) pour plus
	 * d’informations.
	 * @return string
	 */
	public function getThemeConfig() {
		return $this->theme_config;
	}
	
	/**
	 * Ce paramètre est obligatoire. Correspondre à la date locale du site marchand
	 * au format AAAAMMJJHHMMSS.
	 * @param string $value
	 * @return void
	 */
	public function setTransDate( $value ) {
		$value = strval( $value );
		
		if( strlen( $value ) != 14 ) {
			throw new CybPP_Exception( 'Trans Date as bad length', 4 );	
		}
		
		$this->trans_date = $value;
	}
	
	/**
	 * Ce paramètre est obligatoire. Correspondre à la date locale du site marchand
	 * au format AAAAMMJJHHMMSS.
	 * @return string
	 */
	public function getTransDate() {	
		return $this->trans_date;
	}
	
	/**
	 * Ce paramètre est obligatoire. Il est constitué de 6 caractères numériques et doit
	 * être unique pour chaque transaction sur un site donné sur la journée. En effet
	 * l'identifiant unique de transaction au niveau de la plateforme de paiement est
	 * constitué du site_id, de trans_date restreint à la valeur de la journée (partie
	 * correspondant à AAAAMMJJ) et de trans_id. Il est à la charge du site marchand
	 * de garantir cette unicité sur la journée. Il doit être impérativement compris entre
	 * 000000 et 899999. La tranche 900000 et 999999 est interdite.
	 * @param string $value
	 * @return void
	 */
	public function setTransId( $value ){
		$value = strval( $value );
		
		if( strlen( $value ) < 6 ) {
			$value = sprintf("%06s", $value);
		} else if( strlen( $value ) > 6 ) {
			throw new CybPP_Exception( 'Trans Id as bad length', 3 );	
		}
		
		$this->trans_id = $value;
	}
	
	/**
	 * Ce paramètre est obligatoire. Il est constitué de 6 caractères numériques et doit
	 * être unique pour chaque transaction sur un site donné sur la journée. En effet
	 * l'identifiant unique de transaction au niveau de la plateforme de paiement est
	 * constitué du site_id, de trans_date restreint à la valeur de la journée (partie
	 * correspondant à AAAAMMJJ) et de trans_id. Il est à la charge du site marchand
	 * de garantir cette unicité sur la journée. Il doit être impérativement compris entre
	 * 000000 et 899999. La tranche 900000 et 999999 est interdite.
	 * @return string
	 */
	public function getTransId() {	
		return $this->trans_id;
	}
	
	/**
	 * Paramètre obligatoire indiquant si cette transaction devra faire l'objet d'une
	 * validation manuelle de la part du commerçant. Si ce paramètre est vide alors la
	 * configuration par défaut du site sera prise. Cette dernière est paramétrable dans
	 * l’outil de gestion de caisse Cyberplus Paiement par toutes les personnes dûment
	 * habilitées.
	 * @param string $value
	 * @return void
	 */
	public function setValidationMode( $value ) {
		switch( $value ) {
			case CybPP_Const::VALIDATION_MODE_AUTO :
				$this->validation_mode = CybPP_Const::VALIDATION_MODE_AUTO;
				break;
			case CybPP_Const::VALIDATION_MODE_MANUEL :
				$this->validation_mode = CybPP_Const::VALIDATION_MODE_MANUEL;
				break;
			default :
				throw new CybPP_Exception( 'Valdiation Mode as bad value', 5 );
				break;
		}
	}
	
	/**
	 * Paramètre obligatoire indiquant si cette transaction devra faire l'objet d'une
	 * validation manuelle de la part du commerçant. Si ce paramètre est vide alors la
	 * configuration par défaut du site sera prise. Cette dernière est paramétrable dans
	 * l’outil de gestion de caisse Cyberplus Paiement par toutes les personnes dûment
	 * habilitées.
	 * @return string
	 */
	public function getValidationMode() {
		return $this->validation_mode;
	}
	
	/**
	 * Paramètre obligatoire. La version actuelle est V1.
	 * @param string $value
	 * @return void
	 */
	public function setVersion( $value ) {
		switch( $value ) {
			case CybPP_Const::VERSION_V1 :
				$this->version = CybPP_Const::VERSION_V1;
				break;
			default :
				throw new CybPP_Exception( 'Version as bad value', 1 );
				break;
		}
	}
	
	/**
	 * Paramètre obligatoire. La version actuelle est V1.
	 * @return string
	 */
	public function getVersion() {
		return $this->version;
	}
	
	/**
	 * URL facultative où sera redirigé le client en cas de succès du paiement, après
	 * appui du bouton " retourner à la boutique ". Seuls les ports http (port 80) et https
	 * port 443 sont possibles.
	 * @param string $value
	 * @return void
	 */
	public function setUrlSuccess( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 127 ) {
			throw new CybPP_Exception( 'Url Success is too long', 24 );	
		}
		
		$this->url_success = $value;
	}
	
	/**
	 * URL facultative où sera redirigé le client en cas de succès du paiement, après
	 * appui du bouton " retourner à la boutique ". Seuls les ports http (port 80) et https
	 * port 443 sont possibles.
	 * @return string
	 */
	public function getUrlSuccess() {
		return $this->url_success;
	}
	
	/**
	 * URL facultative où sera redirigé le client en cas de refus d’autorisation avec le
	 * code 02 « referral », après appui du bouton " retourner à la boutique ". Seuls les
	 * ports http (port 80) et https port 443 sont possibles.
	 * @param string $value
	 * @return void
	 */
	public function setUrlReferral( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 127 ) {
			throw new CybPP_Exception( 'Url Referral is too long', 26 );	
		}
		
		$this->url_referral = $value;
	}
	
	/**
	 * URL facultative où sera redirigé le client en cas de refus d’autorisation avec le
	 * code 02 « referral », après appui du bouton " retourner à la boutique ". Seuls les
	 * ports http (port 80) et https port 443 sont possibles.
	 * @return string
	 */
	public function getUrlReferral() {
		return $this->url_referral;
	}
	
	/**
	 * URL facultative où sera redirigé le client en cas de refus pour toute autre cause
	 * que le " referral ", après appui du bouton " retourner à la boutique ". Seuls les ports
	 * http (port 80) et https port 443 sont possibles.
	 * @param string $value
	 * @return void
	 */
	public function setUrlRefused( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 127 ) {
			throw new CybPP_Exception( 'Url Refused is too long', 25 );	
		}
		
		$this->url_refused = $value;
	}
	
	/**
	 * URL facultative où sera redirigé le client en cas de refus pour toute autre cause
	 * que le " referral ", après appui du bouton " retourner à la boutique ". Seuls les ports
	 * http (port 80) et https port 443 sont possibles.
	 * @return string
	 */
	public function getUrlRefused() {
		return $this->url_refused;
	}
	
	/**
	 * URL facultative où sera redirigé le client si celui-ci appuie sur " annuler et
	 * retourner à la boutique " avant d'avoir procédé au paiement. Seuls les ports http
	 * (port 80) et https port 443 sont possibles.
	 * @param string $value
	 * @return void
	 */
	public function setUrlCancel( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 127 ) {
			throw new CybPP_Exception( 'Url Cancel is too long', 27 );	
		}
		
		$this->url_cancel = $value;
	}
	
	/**
	 * URL facultative où sera redirigé le client si celui-ci appuie sur " annuler et
	 * retourner à la boutique " avant d'avoir procédé au paiement. Seuls les ports http
	 * (port 80) et https port 443 sont possibles.
	 * @return string
	 */
	public function getUrlCancel() {
		return $this->url_cancel;
	}
	
	/**
	 * URL facultative où sera redirigé le client en cas d'erreur de traitement interne.
	 * Seuls les ports http (port 80) et https port 443 sont possibles.
	 * @param string $value
	 * @return void
	 */
	public function setUrlError( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 127 ) {
			throw new CybPP_Exception( 'Url Error is too long', 29 );	
		}
		
		$this->url_error = $value;
	}
	
	/**
	 * URL facultative où sera redirigé le client en cas d'erreur de traitement interne.
	 * Seuls les ports http (port 80) et https port 443 sont possibles.
	 * @return string
	 */
	public function getUrlError() {
		return $this->url_error;
	}
	
	/**
	 * URL facultative où sera redirigé par défaut le client après un appui sur le bouton 
	 * "retourner à la boutique ", si les URL correspondantes aux cas de figure vus
	 * précédemment ne sont pas renseignées. Seuls les ports http (port 80) et https port
	 * 443 sont possibles.
	 * Si cette URL n’est pas présente dans la requête, alors c’est la configuration dans
	 * l’outil de gestion de caisse qui sera prise en compte.
	 * En effet il est possible de configurer des URL de retour, en mode TEST et en mode
	 * PRODUCTION. Ces paramètres sont nommés « URL de retour de la boutique » et
	 * « URL de retour de la boutique en mode test » respectivement, et sont accessibles
	 * dans l’onglet « Configuration » lors du paramétrage d’une boutique.
	 * Si toutefois aucune URL n’est présente, que ce soit dans la requête ou dans le
	 * paramétrage de la boutique, alors le bouton « retourner à la boutique »
	 * redirigera vers l’URL générique de la boutique (paramètre nommé « URL » dans la
	 * configuration de la boutique).
	 * @param string $value
	 * @return void
	 */
	public function setUrlReturn( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 127 ) {
			throw new CybPP_Exception( 'Url Return is too long', 28 );	
		}
		
		$this->url_return = $value;
	}
	
	/**
	 * URL facultative où sera redirigé par défaut le client après un appui sur le bouton 
	 * "retourner à la boutique ", si les URL correspondantes aux cas de figure vus
	 * précédemment ne sont pas renseignées. Seuls les ports http (port 80) et https port
	 * 443 sont possibles.
	 * Si cette URL n’est pas présente dans la requête, alors c’est la configuration dans
	 * l’outil de gestion de caisse qui sera prise en compte.
	 * En effet il est possible de configurer des URL de retour, en mode TEST et en mode
	 * PRODUCTION. Ces paramètres sont nommés « URL de retour de la boutique » et
	 * « URL de retour de la boutique en mode test » respectivement, et sont accessibles
	 * dans l’onglet « Configuration » lors du paramétrage d’une boutique.
	 * Si toutefois aucune URL n’est présente, que ce soit dans la requête ou dans le
	 * paramétrage de la boutique, alors le bouton « retourner à la boutique »
	 * redirigera vers l’URL générique de la boutique (paramètre nommé « URL » dans la
	 * configuration de la boutique).
	 * @return string
	 */
	public function getUrlReturn() {
		return $this->url_return;
	}
	
	/**
	 * Paramètre facultatif spécifiant des informations complémentaires quant au
	 * paiement. Dans le cas d’un paiement via une saisie manuelle, ce paramètre
	 * contient l’identifiant de l’utilisateur à l’origine de la transaction. Dans les autres
	 * cas de paiement (eMail, téléphone...) tels que définis par le paramètre
	 * payment_src, ce paramètre doit servir à identifier l’opérateur à l’origine de la
	 * transaction.
	 * @param string $value
	 * @return void
	 */
	public function setUserInfo( $value ) {
		$value = strval( $value );
		
		if( $value != null && strlen( $value ) > 255 ) {
			throw new CybPP_Exception( 'User Info is too long', 61 );	
		}
		
		$this->user_info = $value;
	}
	
	/**
	 * Paramètre facultatif spécifiant des informations complémentaires quant au
	 * paiement. Dans le cas d’un paiement via une saisie manuelle, ce paramètre
	 * contient l’identifiant de l’utilisateur à l’origine de la transaction. Dans les autres
	 * cas de paiement (eMail, téléphone...) tels que définis par le paramètre
	 * payment_src, ce paramètre doit servir à identifier l’opérateur à l’origine de la
	 * transaction.
	 * @return string
	 */
	public function getUserInfo() {
		return $this->user_info;
	}
	
	public function getCertificat() {
		return $this->certificat;
	}
	
	/**
	 * permet de calculer la signature
	 * @return void
	 */
	public function processSignature() {
		
		$signature = null;
		$signature .= $this->getVersion();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getSiteId();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getCtxMode();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getTransId();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getTransDate();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getValidationMode();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getCaptureDelay();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getPaymentConfig();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getPaymentCards();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getAmount();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getCurrency();
		$signature .= CybPP_Const::SIGNATURE_SEPARATOR;
		$signature .= $this->getCertificat();
		
		$this->signature = sha1( $signature );
		
	}
	
	/**
	 * permet de calculer la signature
	 * @return string
	 */
	public function getSignature() {
		if( $this->signature === null ) {
			$this->processSignature();
		}
		
		return $this->signature;
	}
	
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
	 * @param string $value
	 * @return void
	 */
	public function setPaymentSrc( $value ) {
		$value = strval( $value );
		
		switch( $value ) {
			case CybPP_Const::PAYMENT_SRC_BO :
			case CybPP_Const::PAYMENT_SRC_CC :
			case CybPP_Const::PAYMENT_SRC_ECOMMERCE :
			case CybPP_Const::PAYMENT_SRC_MOTO :
			case CybPP_Const::PAYMENT_SRC_OTHER :
				$this->payment_src = $value;
				break;
			default :
				throw new CybPP_Exception( 'Payment Src has bad value', 60 );
		}
	}
	
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
	 * @return string
	 */
	public function getPaymentSrc() {
		return $this->payment_src;
	}
	
	/**
	 * permet de recuperer l'url à requeter
	 * @return string
	 */
	public function getUrlAction() {
		return CybPP_Const::QUERY_URL;
	}
	
}