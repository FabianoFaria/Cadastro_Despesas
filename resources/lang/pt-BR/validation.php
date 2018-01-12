<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| as the size rules. Feel free to tweak each of these messages here.
	|
	*/

	"accepted"             => " :attribute deve ser aceito.",
	"active_url"           => " :attribute não é um URL válido.",
	"after"                => " :attribute deve ser uma data após :date.",
	"alpha"                => " :attribute pode somente conter letras.",
	"alpha_dash"           => " :attribute pode conter somente letras, números e travesões.",
	"alpha_num"            => " :attribute pode somente conter letras e números.",
	"array"                => " :attribute deve ser uma lista.",
	"before"               => " :attribute deve ser uma data depois de :date.",
	"between"              => array(
		"numeric" => " :attribute deve ter entre :min e :max.",
		"file"    => " :attribute deve ter entre :min e :max kilobytes.",
		"string"  => " :attribute deve ter entre :min e :max characters.",
		"array"   => " :attribute deve ter entre :min e :max items.",
	),
	"confirmed"            => " :attribute confirmação não combinam.",
	"date"                 => " :attribute não é uma data válida.",
	"date_format"          => " :attribute não combinam com o formato :format.",
	"different"            => " :attribute e :other must be different devem se diferentes.",
	"digits"               => " :attribute deve ter :digits digitos.",
	"digits_between"       => " :attribute deve ter entre :min e :max digits.",
	"email"                => " :attribute deve ser um endereço de email válido.",
	"exists"               => " :attribute selecionado não é válido.",
	"image"                => " :attribute deve ser uma imagem.",
	"in"                   => " :attribute selecionado é inválido.",
	"integer"              => " :attribute deve ser um número inteiro.",
	"ip"                   => " :attribute deve ser um endereço de IP válido must be a valido.",
	"max"                  => array(
		"numeric" => " :attribute não pode ser maior que :max.",
		"file"    => " :attribute não pode ser maior do que :max kilobytes.",
		"string"  => " :attribute não pode ser maior do que :max characters.",
		"array"   => " :attribute não pode ter mais do que :max items.",
	),
	"mimes"                => "The :attribute must be a file of type: :values.",
	"min"                  => array(
		"numeric" => " :attribute deve ter pelo menos :min quantidades.",
		"file"    => " :attribute deve ter pelo menos :min kilobytes.",
		"string"  => " :attribute deve ter pelo menos :min characters.",
		"array"   => " :attribute deve ter pelo menos :min items.",
	),
	"not_in"               => "O :attribute selecionado é inválido.",
	"numeric"              => " :attribute deve ser um número.",
	"regex"                => "O formato de :attribute não é válido.",


	"required"             => "Campo :attribute é obrigatório.",
	"required_if"          => "Campo :attribute é obrigatório quando :other é :value.",
	"required_with"        => "Campo :attribute é obrigatório quando :values esta presente.",
	"required_with_all"    => "Campo :attribute é obrigatório quando :values está presente.",
	"required_without"     => "Campo :attribute é obrigatório quando :values não está presente.",
	
	"required_without_all" => " :attribute campo é obrigatório quando nenhum dos :values estão presentes.",
	"same"                 => " :attribute e :other devem ser iguais.",

	"size"                 => array(
		"numeric" => "Campo :attribute deve ser :size.",
		"file"    => "Campo :attribute deve ser :size kilobytes.",
		"string"  => "Campo :attribute deve ter :size caracteres.",
		"array"   => "Campo :attribute deve conter :size items.",
	),
	"unique"               => " :attribute já existe no sistema.",
	"url"                  => " :attribute está no formato inválido.",
	"cpf_cnpj"             => "CPF ou CNPJ não foi digitado corretamente.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => array(
		'attribute-name' => array(
			'rule-name' => 'custom-message',
			'cpfCnpj'   => 'CPF ou CNPJ não foi digitado corretamente.',
		),
	),

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => array(),

);
