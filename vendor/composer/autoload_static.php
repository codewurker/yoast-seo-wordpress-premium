<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf82072f1f0ebcd1562caedb68be83ef3
{
    public static $files = array (
        '81db02b30f563b92907e271b66bd7559' => __DIR__ . '/..' . '/yoast/whip/src/Facades/wordpress.php',
    );

    public static $prefixLengthsPsr4 = array (
        'Y' => 
        array (
            'Yoast\\WHIPv2\\' => 13,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Yoast\\WHIPv2\\' => 
        array (
            0 => __DIR__ . '/..' . '/yoast/whip/src',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'WPSEO_CLI_Premium_Requirement' => __DIR__ . '/../..' . '/cli/cli-premium-requirement.php',
        'WPSEO_CLI_Redirect_Base_Command' => __DIR__ . '/../..' . '/cli/cli-redirect-base-command.php',
        'WPSEO_CLI_Redirect_Command_Namespace' => __DIR__ . '/../..' . '/cli/cli-redirect-command-namespace.php',
        'WPSEO_CLI_Redirect_Create_Command' => __DIR__ . '/../..' . '/cli/cli-redirect-create-command.php',
        'WPSEO_CLI_Redirect_Delete_Command' => __DIR__ . '/../..' . '/cli/cli-redirect-delete-command.php',
        'WPSEO_CLI_Redirect_Follow_Command' => __DIR__ . '/../..' . '/cli/cli-redirect-follow-command.php',
        'WPSEO_CLI_Redirect_Has_Command' => __DIR__ . '/../..' . '/cli/cli-redirect-has-command.php',
        'WPSEO_CLI_Redirect_List_Command' => __DIR__ . '/../..' . '/cli/cli-redirect-list-command.php',
        'WPSEO_CLI_Redirect_Update_Command' => __DIR__ . '/../..' . '/cli/cli-redirect-update-command.php',
        'WPSEO_Custom_Fields_Plugin' => __DIR__ . '/../..' . '/classes/custom-fields-plugin.php',
        'WPSEO_Executable_Redirect' => __DIR__ . '/../..' . '/classes/redirect/executable-redirect.php',
        'WPSEO_Export_Keywords_CSV' => __DIR__ . '/../..' . '/classes/export/export-keywords-csv.php',
        'WPSEO_Export_Keywords_Post_Presenter' => __DIR__ . '/../..' . '/classes/export/export-keywords-post-presenter.php',
        'WPSEO_Export_Keywords_Post_Query' => __DIR__ . '/../..' . '/classes/export/export-keywords-post-query.php',
        'WPSEO_Export_Keywords_Presenter' => __DIR__ . '/../..' . '/classes/export/export-keywords-presenter-interface.php',
        'WPSEO_Export_Keywords_Query' => __DIR__ . '/../..' . '/classes/export/export-keywords-query-interface.php',
        'WPSEO_Export_Keywords_Term_Presenter' => __DIR__ . '/../..' . '/classes/export/export-keywords-term-presenter.php',
        'WPSEO_Export_Keywords_Term_Query' => __DIR__ . '/../..' . '/classes/export/export-keywords-term-query.php',
        'WPSEO_HTML_Diff_Renderer' => __DIR__ . '/../..' . '/classes/html-diff-renderer.php',
        'WPSEO_Metabox_Link_Suggestions' => __DIR__ . '/../..' . '/classes/metabox-link-suggestions.php',
        'WPSEO_Multi_Keyword' => __DIR__ . '/../..' . '/classes/multi-keyword.php',
        'WPSEO_Post_Watcher' => __DIR__ . '/../..' . '/classes/post-watcher.php',
        'WPSEO_Premium' => __DIR__ . '/../..' . '/premium.php',
        'WPSEO_Premium_Asset_JS_L10n' => __DIR__ . '/../..' . '/classes/premium-asset-js-l10n.php',
        'WPSEO_Premium_Assets' => __DIR__ . '/../..' . '/classes/premium-assets.php',
        'WPSEO_Premium_Expose_Shortlinks' => __DIR__ . '/../..' . '/classes/premium-expose-shortlinks.php',
        'WPSEO_Premium_Import_Manager' => __DIR__ . '/../..' . '/classes/premium-import-manager.php',
        'WPSEO_Premium_Javascript_Strings' => __DIR__ . '/../..' . '/classes/premium-javascript-strings.php',
        'WPSEO_Premium_Keyword_Export_Manager' => __DIR__ . '/../..' . '/classes/premium-keyword-export-manager.php',
        'WPSEO_Premium_Metabox' => __DIR__ . '/../..' . '/classes/premium-metabox.php',
        'WPSEO_Premium_Option' => __DIR__ . '/../..' . '/classes/premium-option.php',
        'WPSEO_Premium_Orphaned_Content_Support' => __DIR__ . '/../..' . '/classes/premium-orphaned-content-support.php',
        'WPSEO_Premium_Orphaned_Content_Utils' => __DIR__ . '/../..' . '/classes/premium-orphaned-content-utils.php',
        'WPSEO_Premium_Orphaned_Post_Filter' => __DIR__ . '/../..' . '/classes/premium-orphaned-post-filter.php',
        'WPSEO_Premium_Orphaned_Post_Query' => __DIR__ . '/../..' . '/classes/premium-orphaned-post-query.php',
        'WPSEO_Premium_Prominent_Words_Support' => __DIR__ . '/../..' . '/classes/premium-prominent-words-support.php',
        'WPSEO_Premium_Prominent_Words_Unindexed_Post_Query' => __DIR__ . '/../..' . '/classes/premium-prominent-words-unindexed-post-query.php',
        'WPSEO_Premium_Prominent_Words_Versioning' => __DIR__ . '/../..' . '/classes/premium-prominent-words-versioning.php',
        'WPSEO_Premium_Redirect_EndPoint' => __DIR__ . '/../..' . '/classes/premium-redirect-endpoint.php',
        'WPSEO_Premium_Redirect_Export_Manager' => __DIR__ . '/../..' . '/classes/premium-redirect-export-manager.php',
        'WPSEO_Premium_Redirect_Option' => __DIR__ . '/../..' . '/classes/premium-redirect-option.php',
        'WPSEO_Premium_Redirect_Service' => __DIR__ . '/../..' . '/classes/premium-redirect-service.php',
        'WPSEO_Premium_Redirect_Undo_EndPoint' => __DIR__ . '/../..' . '/classes/redirect-undo-endpoint.php',
        'WPSEO_Premium_Register_Capabilities' => __DIR__ . '/../..' . '/classes/premium-register-capabilities.php',
        'WPSEO_Premium_Stale_Cornerstone_Content_Filter' => __DIR__ . '/../..' . '/classes/premium-stale-cornerstone-content-filter.php',
        'WPSEO_Product_Premium' => __DIR__ . '/../..' . '/classes/product-premium.php',
        'WPSEO_Redirect' => __DIR__ . '/../..' . '/classes/redirect/redirect.php',
        'WPSEO_Redirect_Abstract_Loader' => __DIR__ . '/../..' . '/classes/redirect/loaders/redirect-abstract-loader.php',
        'WPSEO_Redirect_Abstract_Validation' => __DIR__ . '/../..' . '/classes/redirect/validation/redirect-abstract-validation.php',
        'WPSEO_Redirect_Accessible_Validation' => __DIR__ . '/../..' . '/classes/redirect/validation/redirect-accessible-validation.php',
        'WPSEO_Redirect_Ajax' => __DIR__ . '/../..' . '/classes/redirect/redirect-ajax.php',
        'WPSEO_Redirect_Apache_Exporter' => __DIR__ . '/../..' . '/classes/redirect/exporters/redirect-apache-exporter.php',
        'WPSEO_Redirect_CSV_Exporter' => __DIR__ . '/../..' . '/classes/redirect/exporters/redirect-csv-exporter.php',
        'WPSEO_Redirect_CSV_Loader' => __DIR__ . '/../..' . '/classes/redirect/loaders/redirect-csv-loader.php',
        'WPSEO_Redirect_Endpoint_Validation' => __DIR__ . '/../..' . '/classes/redirect/validation/redirect-endpoint-validation.php',
        'WPSEO_Redirect_Exporter' => __DIR__ . '/../..' . '/classes/redirect/exporters/redirect-exporter-interface.php',
        'WPSEO_Redirect_File_Exporter' => __DIR__ . '/../..' . '/classes/redirect/exporters/redirect-file-exporter.php',
        'WPSEO_Redirect_File_Util' => __DIR__ . '/../..' . '/classes/redirect/redirect-file-util.php',
        'WPSEO_Redirect_Form_Presenter' => __DIR__ . '/../..' . '/classes/redirect/presenters/redirect-form-presenter.php',
        'WPSEO_Redirect_Formats' => __DIR__ . '/../..' . '/classes/redirect/redirect-formats.php',
        'WPSEO_Redirect_Formatter' => __DIR__ . '/../..' . '/classes/redirect/redirect-formatter.php',
        'WPSEO_Redirect_HTAccess_Loader' => __DIR__ . '/../..' . '/classes/redirect/loaders/redirect-htaccess-loader.php',
        'WPSEO_Redirect_Htaccess_Exporter' => __DIR__ . '/../..' . '/classes/redirect/exporters/redirect-htaccess-exporter.php',
        'WPSEO_Redirect_Htaccess_Util' => __DIR__ . '/../..' . '/classes/redirect/redirect-htaccess-util.php',
        'WPSEO_Redirect_Import_Exception' => __DIR__ . '/../..' . '/classes/redirect/redirect-import-exception.php',
        'WPSEO_Redirect_Importer' => __DIR__ . '/../..' . '/classes/redirect/redirect-importer.php',
        'WPSEO_Redirect_Loader' => __DIR__ . '/../..' . '/classes/redirect/loaders/redirect-loader-interface.php',
        'WPSEO_Redirect_Manager' => __DIR__ . '/../..' . '/classes/redirect/redirect-manager.php',
        'WPSEO_Redirect_Nginx_Exporter' => __DIR__ . '/../..' . '/classes/redirect/exporters/redirect-nginx-exporter.php',
        'WPSEO_Redirect_Option' => __DIR__ . '/../..' . '/classes/redirect/redirect-option.php',
        'WPSEO_Redirect_Option_Exporter' => __DIR__ . '/../..' . '/classes/redirect/exporters/redirect-option-exporter.php',
        'WPSEO_Redirect_Page' => __DIR__ . '/../..' . '/classes/redirect/redirect-page.php',
        'WPSEO_Redirect_Page_Presenter' => __DIR__ . '/../..' . '/classes/redirect/presenters/redirect-page-presenter.php',
        'WPSEO_Redirect_Presence_Validation' => __DIR__ . '/../..' . '/classes/redirect/validation/redirect-presence-validation.php',
        'WPSEO_Redirect_Presenter' => __DIR__ . '/../..' . '/classes/redirect/presenters/redirect-presenter-interface.php',
        'WPSEO_Redirect_Quick_Edit_Presenter' => __DIR__ . '/../..' . '/classes/redirect/presenters/redirect-quick-edit-presenter.php',
        'WPSEO_Redirect_Redirection_Loader' => __DIR__ . '/../..' . '/classes/redirect/loaders/redirect-redirection-loader.php',
        'WPSEO_Redirect_Relative_Origin_Validation' => __DIR__ . '/../..' . '/classes/redirect/validation/redirect-relative-origin-validation.php',
        'WPSEO_Redirect_Safe_Redirect_Loader' => __DIR__ . '/../..' . '/classes/redirect/loaders/redirect-safe-redirect-loader.php',
        'WPSEO_Redirect_Self_Redirect_Validation' => __DIR__ . '/../..' . '/classes/redirect/validation/redirect-self-redirect-validation.php',
        'WPSEO_Redirect_Settings_Presenter' => __DIR__ . '/../..' . '/classes/redirect/presenters/redirect-settings-presenter.php',
        'WPSEO_Redirect_Simple_301_Redirect_Loader' => __DIR__ . '/../..' . '/classes/redirect/loaders/redirect-simple-301-redirect-loader.php',
        'WPSEO_Redirect_Sitemap_Filter' => __DIR__ . '/../..' . '/classes/redirect/redirect-sitemap-filter.php',
        'WPSEO_Redirect_Subdirectory_Validation' => __DIR__ . '/../..' . '/classes/redirect/validation/redirect-subdirectory-validation.php',
        'WPSEO_Redirect_Tab_Presenter' => __DIR__ . '/../..' . '/classes/redirect/presenters/redirect-tab-presenter.php',
        'WPSEO_Redirect_Table' => __DIR__ . '/../..' . '/classes/redirect/redirect-table.php',
        'WPSEO_Redirect_Table_Presenter' => __DIR__ . '/../..' . '/classes/redirect/presenters/redirect-table-presenter.php',
        'WPSEO_Redirect_Types' => __DIR__ . '/../..' . '/classes/redirect/redirect-types.php',
        'WPSEO_Redirect_Uniqueness_Validation' => __DIR__ . '/../..' . '/classes/redirect/validation/redirect-uniqueness-validation.php',
        'WPSEO_Redirect_Upgrade' => __DIR__ . '/../..' . '/classes/redirect/redirect-upgrade.php',
        'WPSEO_Redirect_Url_Formatter' => __DIR__ . '/../..' . '/classes/redirect/redirect-url-formatter.php',
        'WPSEO_Redirect_Util' => __DIR__ . '/../..' . '/classes/redirect/redirect-util.php',
        'WPSEO_Redirect_Validation' => __DIR__ . '/../..' . '/classes/redirect/validation/redirect-validation-interface.php',
        'WPSEO_Redirect_Validator' => __DIR__ . '/../..' . '/classes/redirect/redirect-validator.php',
        'WPSEO_Social_Previews' => __DIR__ . '/../..' . '/classes/social-previews.php',
        'WPSEO_Term_Watcher' => __DIR__ . '/../..' . '/classes/term-watcher.php',
        'WPSEO_Upgrade_Manager' => __DIR__ . '/../..' . '/classes/upgrade-manager.php',
        'WPSEO_Validation_Error' => __DIR__ . '/../..' . '/classes/validation-error.php',
        'WPSEO_Validation_Result' => __DIR__ . '/../..' . '/classes/validation-result.php',
        'WPSEO_Validation_Warning' => __DIR__ . '/../..' . '/classes/validation-warning.php',
        'WPSEO_Watcher' => __DIR__ . '/../..' . '/classes/watcher.php',
        'Yoast\\WHIPv2\\Configuration' => __DIR__ . '/..' . '/yoast/whip/src/Configuration.php',
        'Yoast\\WHIPv2\\Exceptions\\EmptyProperty' => __DIR__ . '/..' . '/yoast/whip/src/Exceptions/EmptyProperty.php',
        'Yoast\\WHIPv2\\Exceptions\\InvalidOperatorType' => __DIR__ . '/..' . '/yoast/whip/src/Exceptions/InvalidOperatorType.php',
        'Yoast\\WHIPv2\\Exceptions\\InvalidType' => __DIR__ . '/..' . '/yoast/whip/src/Exceptions/InvalidType.php',
        'Yoast\\WHIPv2\\Exceptions\\InvalidVersionComparisonString' => __DIR__ . '/..' . '/yoast/whip/src/Exceptions/InvalidVersionComparisonString.php',
        'Yoast\\WHIPv2\\Host' => __DIR__ . '/..' . '/yoast/whip/src/Host.php',
        'Yoast\\WHIPv2\\Interfaces\\DismissStorage' => __DIR__ . '/..' . '/yoast/whip/src/Interfaces/DismissStorage.php',
        'Yoast\\WHIPv2\\Interfaces\\Listener' => __DIR__ . '/..' . '/yoast/whip/src/Interfaces/Listener.php',
        'Yoast\\WHIPv2\\Interfaces\\Message' => __DIR__ . '/..' . '/yoast/whip/src/Interfaces/Message.php',
        'Yoast\\WHIPv2\\Interfaces\\MessagePresenter' => __DIR__ . '/..' . '/yoast/whip/src/Interfaces/MessagePresenter.php',
        'Yoast\\WHIPv2\\Interfaces\\Requirement' => __DIR__ . '/..' . '/yoast/whip/src/Interfaces/Requirement.php',
        'Yoast\\WHIPv2\\Interfaces\\VersionDetector' => __DIR__ . '/..' . '/yoast/whip/src/Interfaces/VersionDetector.php',
        'Yoast\\WHIPv2\\MessageDismisser' => __DIR__ . '/..' . '/yoast/whip/src/MessageDismisser.php',
        'Yoast\\WHIPv2\\MessageFormatter' => __DIR__ . '/..' . '/yoast/whip/src/MessageFormatter.php',
        'Yoast\\WHIPv2\\MessagesManager' => __DIR__ . '/..' . '/yoast/whip/src/MessagesManager.php',
        'Yoast\\WHIPv2\\Messages\\BasicMessage' => __DIR__ . '/..' . '/yoast/whip/src/Messages/BasicMessage.php',
        'Yoast\\WHIPv2\\Messages\\HostMessage' => __DIR__ . '/..' . '/yoast/whip/src/Messages/HostMessage.php',
        'Yoast\\WHIPv2\\Messages\\InvalidVersionRequirementMessage' => __DIR__ . '/..' . '/yoast/whip/src/Messages/InvalidVersionRequirementMessage.php',
        'Yoast\\WHIPv2\\Messages\\NullMessage' => __DIR__ . '/..' . '/yoast/whip/src/Messages/NullMessage.php',
        'Yoast\\WHIPv2\\Messages\\UpgradePhpMessage' => __DIR__ . '/..' . '/yoast/whip/src/Messages/UpgradePhpMessage.php',
        'Yoast\\WHIPv2\\Presenters\\WPMessagePresenter' => __DIR__ . '/..' . '/yoast/whip/src/Presenters/WPMessagePresenter.php',
        'Yoast\\WHIPv2\\RequirementsChecker' => __DIR__ . '/..' . '/yoast/whip/src/RequirementsChecker.php',
        'Yoast\\WHIPv2\\VersionRequirement' => __DIR__ . '/..' . '/yoast/whip/src/VersionRequirement.php',
        'Yoast\\WHIPv2\\WPDismissOption' => __DIR__ . '/..' . '/yoast/whip/src/WPDismissOption.php',
        'Yoast\\WHIPv2\\WPMessageDismissListener' => __DIR__ . '/..' . '/yoast/whip/src/WPMessageDismissListener.php',
        'Yoast\\WP\\SEO\\Config\\Migrations\\WpYoastPremiumImprovedInternalLinking' => __DIR__ . '/../..' . '/src/config/migrations/20190715101200_WpYoastPremiumImprovedInternalLinking.php',
        'Yoast\\WP\\SEO\\Integrations\\Blocks\\Siblings_Block' => __DIR__ . '/../..' . '/classes/blocks/siblings-block.php',
        'Yoast\\WP\\SEO\\Integrations\\Blocks\\Subpages_Block' => __DIR__ . '/../..' . '/classes/blocks/subpages-block.php',
        'Yoast\\WP\\SEO\\Integrations\\Third_Party\\TranslationsPress' => __DIR__ . '/../..' . '/src/integrations/third-party/translationspress.php',
        'Yoast\\WP\\SEO\\Integrations\\Third_Party\\Wincher_Keyphrases' => __DIR__ . '/../..' . '/src/integrations/third-party/wincher-keyphrases.php',
        'Yoast\\WP\\SEO\\Models\\Prominent_Words' => __DIR__ . '/../..' . '/src/models/prominent-words.php',
        'Yoast\\WP\\SEO\\Premium\\AI_Suggestions_Postprocessor\\Application\\AI_Suggestions_Serializer' => __DIR__ . '/../..' . '/src/ai-suggestions-postprocessor/application/ai-suggestions-serializer.php',
        'Yoast\\WP\\SEO\\Premium\\AI_Suggestions_Postprocessor\\Application\\AI_Suggestions_Unifier' => __DIR__ . '/../..' . '/src/ai-suggestions-postprocessor/application/ai-suggestions-unifier.php',
        'Yoast\\WP\\SEO\\Premium\\AI_Suggestions_Postprocessor\\Application\\Sentence_Processor' => __DIR__ . '/../..' . '/src/ai-suggestions-postprocessor/application/sentence-processor.php',
        'Yoast\\WP\\SEO\\Premium\\AI_Suggestions_Postprocessor\\Application\\Suggestion_Processor' => __DIR__ . '/../..' . '/src/ai-suggestions-postprocessor/application/suggestion-processor.php',
        'Yoast\\WP\\SEO\\Premium\\AI_Suggestions_Postprocessor\\Domain\\Suggestion' => __DIR__ . '/../..' . '/src/ai-suggestions-postprocessor/domain/suggestion.php',
        'Yoast\\WP\\SEO\\Premium\\AI_Suggestions_Postprocessor\\Domain\\Suggestion_Interface' => __DIR__ . '/../..' . '/src/ai-suggestions-postprocessor/domain/suggestion-interface.php',
        'Yoast\\WP\\SEO\\Premium\\Actions\\AI_Generator_Action' => __DIR__ . '/../..' . '/src/actions/ai-generator-action.php',
        'Yoast\\WP\\SEO\\Premium\\Actions\\Link_Suggestions_Action' => __DIR__ . '/../..' . '/src/actions/link-suggestions-action.php',
        'Yoast\\WP\\SEO\\Premium\\Actions\\Prominent_Words\\Complete_Action' => __DIR__ . '/../..' . '/src/actions/prominent-words/complete-action.php',
        'Yoast\\WP\\SEO\\Premium\\Actions\\Prominent_Words\\Content_Action' => __DIR__ . '/../..' . '/src/actions/prominent-words/content-action.php',
        'Yoast\\WP\\SEO\\Premium\\Actions\\Prominent_Words\\Save_Action' => __DIR__ . '/../..' . '/src/actions/prominent-words/save-action.php',
        'Yoast\\WP\\SEO\\Premium\\Addon_Installer' => __DIR__ . '/../..' . '/src/addon-installer.php',
        'Yoast\\WP\\SEO\\Premium\\Conditionals\\Ai_Editor_Conditional' => __DIR__ . '/../..' . '/src/conditionals/ai-editor-conditional.php',
        'Yoast\\WP\\SEO\\Premium\\Conditionals\\Algolia_Enabled_Conditional' => __DIR__ . '/../..' . '/src/conditionals/algolia-enabled-conditional.php',
        'Yoast\\WP\\SEO\\Premium\\Conditionals\\Cornerstone_Enabled_Conditional' => __DIR__ . '/../..' . '/src/conditionals/cornerstone-enabled-conditional.php',
        'Yoast\\WP\\SEO\\Premium\\Conditionals\\EDD_Conditional' => __DIR__ . '/../..' . '/src/conditionals/edd-conditional.php',
        'Yoast\\WP\\SEO\\Premium\\Conditionals\\Inclusive_Language_Enabled_Conditional' => __DIR__ . '/../..' . '/src/conditionals/inclusive-language-enabled-conditional.php',
        'Yoast\\WP\\SEO\\Premium\\Conditionals\\Not_Woo_Order_Conditional' => __DIR__ . '/../..' . '/src/conditionals/not-woo-order-conditional.php',
        'Yoast\\WP\\SEO\\Premium\\Conditionals\\Term_Overview_Or_Ajax_Conditional' => __DIR__ . '/../..' . '/src/conditionals/term-overview-or-ajax-conditional.php',
        'Yoast\\WP\\SEO\\Premium\\Conditionals\\Yoast_Admin_Or_Introductions_Route_Conditional' => __DIR__ . '/../..' . '/src/conditionals/yoast-admin-or-introductions-route-conditional.php',
        'Yoast\\WP\\SEO\\Premium\\Config\\Badge_Group_Names' => __DIR__ . '/../..' . '/src/config/badge-group-names.php',
        'Yoast\\WP\\SEO\\Premium\\Config\\Migrations\\AddIndexOnIndexableIdAndStem' => __DIR__ . '/../..' . '/src/config/migrations/20210827093024_AddIndexOnIndexableIdAndStem.php',
        'Yoast\\WP\\SEO\\Premium\\DOM_Manager\\Application\\DOM_Parser' => __DIR__ . '/../..' . '/src/dom-manager/application/dom-parser.php',
        'Yoast\\WP\\SEO\\Premium\\DOM_Manager\\Application\\Node_Processor' => __DIR__ . '/../..' . '/src/dom-manager/application/node-processor.php',
        'Yoast\\WP\\SEO\\Premium\\Database\\Migration_Runner_Premium' => __DIR__ . '/../..' . '/src/database/migration-runner-premium.php',
        'Yoast\\WP\\SEO\\Premium\\Exceptions\\Forbidden_Property_Mutation_Exception' => __DIR__ . '/../..' . '/src/exceptions/forbidden-property-mutation-exception.php',
        'Yoast\\WP\\SEO\\Premium\\Exceptions\\Remote_Request\\Bad_Request_Exception' => __DIR__ . '/../..' . '/src/exceptions/remote-request/bad-request-exception.php',
        'Yoast\\WP\\SEO\\Premium\\Exceptions\\Remote_Request\\Forbidden_Exception' => __DIR__ . '/../..' . '/src/exceptions/remote-request/forbidden-exception.php',
        'Yoast\\WP\\SEO\\Premium\\Exceptions\\Remote_Request\\Internal_Server_Error_Exception' => __DIR__ . '/../..' . '/src/exceptions/remote-request/internal-server-error-exception.php',
        'Yoast\\WP\\SEO\\Premium\\Exceptions\\Remote_Request\\Not_Found_Exception' => __DIR__ . '/../..' . '/src/exceptions/remote-request/not-found-exception.php',
        'Yoast\\WP\\SEO\\Premium\\Exceptions\\Remote_Request\\Payment_Required_Exception' => __DIR__ . '/../..' . '/src/exceptions/remote-request/payment-required-exception.php',
        'Yoast\\WP\\SEO\\Premium\\Exceptions\\Remote_Request\\Remote_Request_Exception' => __DIR__ . '/../..' . '/src/exceptions/remote-request/remote-request-exception.php',
        'Yoast\\WP\\SEO\\Premium\\Exceptions\\Remote_Request\\Request_Timeout_Exception' => __DIR__ . '/../..' . '/src/exceptions/remote-request/request-timeout-exception.php',
        'Yoast\\WP\\SEO\\Premium\\Exceptions\\Remote_Request\\Service_Unavailable_Exception' => __DIR__ . '/../..' . '/src/exceptions/remote-request/service-unavailable-exception.php',
        'Yoast\\WP\\SEO\\Premium\\Exceptions\\Remote_Request\\Too_Many_Requests_Exception' => __DIR__ . '/../..' . '/src/exceptions/remote-request/too-many-requests-exception.php',
        'Yoast\\WP\\SEO\\Premium\\Exceptions\\Remote_Request\\Unauthorized_Exception' => __DIR__ . '/../..' . '/src/exceptions/remote-request/unauthorized-exception.php',
        'Yoast\\WP\\SEO\\Premium\\Exceptions\\Remote_Request\\WP_Request_Exception' => __DIR__ . '/../..' . '/src/exceptions/remote-request/wp-request-exception.php',
        'Yoast\\WP\\SEO\\Premium\\Generated\\Cached_Container' => __DIR__ . '/../..' . '/src/generated/container.php',
        'Yoast\\WP\\SEO\\Premium\\Helpers\\AI_Generator_Helper' => __DIR__ . '/../..' . '/src/helpers/ai-generator-helper.php',
        'Yoast\\WP\\SEO\\Premium\\Helpers\\Current_Page_Helper' => __DIR__ . '/../..' . '/src/helpers/current-page-helper.php',
        'Yoast\\WP\\SEO\\Premium\\Helpers\\Prominent_Words_Helper' => __DIR__ . '/../..' . '/src/helpers/prominent-words-helper.php',
        'Yoast\\WP\\SEO\\Premium\\Helpers\\Version_Helper' => __DIR__ . '/../..' . '/src/helpers/version-helper.php',
        'Yoast\\WP\\SEO\\Premium\\Initializers\\Index_Now_Key' => __DIR__ . '/../..' . '/src/initializers/index-now-key.php',
        'Yoast\\WP\\SEO\\Premium\\Initializers\\Introductions_Initializer' => __DIR__ . '/../..' . '/src/initializers/introductions-initializer.php',
        'Yoast\\WP\\SEO\\Premium\\Initializers\\Plugin' => __DIR__ . '/../..' . '/src/initializers/plugin.php',
        'Yoast\\WP\\SEO\\Premium\\Initializers\\Redirect_Handler' => __DIR__ . '/../..' . '/src/initializers/redirect-handler.php',
        'Yoast\\WP\\SEO\\Premium\\Initializers\\Woocommerce' => __DIR__ . '/../..' . '/src/initializers/woocommerce.php',
        'Yoast\\WP\\SEO\\Premium\\Initializers\\Wp_Cli_Initializer' => __DIR__ . '/../..' . '/src/initializers/wp-cli-initializer.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Abstract_OpenGraph_Integration' => __DIR__ . '/../..' . '/src/integrations/abstract-opengraph-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Admin\\Ai_Consent_Integration' => __DIR__ . '/../..' . '/src/integrations/admin/ai-consent-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Admin\\Ai_Fix_Assessments_Integration' => __DIR__ . '/../..' . '/src/integrations/admin/ai-fix-assessments-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Admin\\Ai_Generator_Integration' => __DIR__ . '/../..' . '/src/integrations/admin/ai-generator-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Admin\\Cornerstone_Column_Integration' => __DIR__ . '/../..' . '/src/integrations/admin/cornerstone-column-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Admin\\Cornerstone_Taxonomy_Column_Integration' => __DIR__ . '/../..' . '/src/integrations/admin/cornerstone-taxonomy-column-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Admin\\Inclusive_Language_Column_Integration' => __DIR__ . '/../..' . '/src/integrations/admin/inclusive-language-column-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Admin\\Inclusive_Language_Filter_Integration' => __DIR__ . '/../..' . '/src/integrations/admin/inclusive-language-filter-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Admin\\Inclusive_Language_Taxonomy_Column_Integration' => __DIR__ . '/../..' . '/src/integrations/admin/inclusive-language-taxonomy-column-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Admin\\Keyword_Integration' => __DIR__ . '/../..' . '/src/integrations/admin/keyword-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Admin\\Metabox_Formatter_Integration' => __DIR__ . '/../..' . '/src/integrations/admin/metabox-formatter-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Admin\\Plugin_Links_Integration' => __DIR__ . '/../..' . '/src/integrations/admin/plugin-links-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Admin\\Prominent_Words\\Indexing_Integration' => __DIR__ . '/../..' . '/src/integrations/admin/prominent-words/indexing-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Admin\\Prominent_Words\\Metabox_Integration' => __DIR__ . '/../..' . '/src/integrations/admin/prominent-words/metabox-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Admin\\Related_Keyphrase_Filter_Integration' => __DIR__ . '/../..' . '/src/integrations/admin/related-keyphrase-filter-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Admin\\Replacement_Variables_Integration' => __DIR__ . '/../..' . '/src/integrations/admin/replacement-variables-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Admin\\Settings_Integration' => __DIR__ . '/../..' . '/src/integrations/admin/settings-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Admin\\Thank_You_Page_Integration' => __DIR__ . '/../..' . '/src/integrations/admin/thank-you-page-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Admin\\Update_Premium_Notification' => __DIR__ . '/../..' . '/src/integrations/admin/update-premium-notification.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Admin\\User_Profile_Integration' => __DIR__ . '/../..' . '/src/integrations/admin/user-profile-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Admin\\Workouts_Integration' => __DIR__ . '/../..' . '/src/integrations/admin/workouts-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Alerts\\Ai_Generator_Tip_Notification' => __DIR__ . '/../..' . '/src/integrations/alerts/ai-generator-tip-notification.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Blocks\\Estimated_Reading_Time_Block' => __DIR__ . '/../..' . '/src/integrations/blocks/estimated-reading-time-block.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Blocks\\Related_Links_Block' => __DIR__ . '/../..' . '/src/integrations/blocks/related-links-block.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Cleanup_Integration' => __DIR__ . '/../..' . '/src/integrations/cleanup-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Front_End\\Robots_Txt_Integration' => __DIR__ . '/../..' . '/src/integrations/front-end/robots-txt-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Frontend_Inspector' => __DIR__ . '/../..' . '/src/integrations/frontend-inspector.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Index_Now_Ping' => __DIR__ . '/../..' . '/src/integrations/index-now-ping.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Missing_Indexables_Count_Integration' => __DIR__ . '/../..' . '/src/integrations/missing-indexables-count-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\OpenGraph_Author_Archive' => __DIR__ . '/../..' . '/src/integrations/opengraph-author-archive.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\OpenGraph_Date_Archive' => __DIR__ . '/../..' . '/src/integrations/opengraph-date-archive.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\OpenGraph_PostType_Archive' => __DIR__ . '/../..' . '/src/integrations/opengraph-posttype-archive.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\OpenGraph_Post_Type' => __DIR__ . '/../..' . '/src/integrations/opengraph-post-type.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\OpenGraph_Term_Archive' => __DIR__ . '/../..' . '/src/integrations/opengraph-term-archive.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Organization_Schema_Integration' => __DIR__ . '/../..' . '/src/integrations/organization-schema-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Publishing_Principles_Schema_Integration' => __DIR__ . '/../..' . '/src/integrations/publishing-principles-schema-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Routes\\AI_Generator_Route' => __DIR__ . '/../..' . '/src/integrations/routes/ai-generator-route.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Routes\\Workouts_Routes_Integration' => __DIR__ . '/../..' . '/src/integrations/routes/workouts-routes-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Third_Party\\Algolia' => __DIR__ . '/../..' . '/src/integrations/third-party/algolia.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Third_Party\\EDD' => __DIR__ . '/../..' . '/src/integrations/third-party/edd.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Third_Party\\Elementor_Premium' => __DIR__ . '/../..' . '/src/integrations/third-party/elementor-premium.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Third_Party\\Elementor_Preview' => __DIR__ . '/../..' . '/src/integrations/third-party/elementor-preview.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Third_Party\\Mastodon' => __DIR__ . '/../..' . '/src/integrations/third-party/mastodon.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Upgrade_Integration' => __DIR__ . '/../..' . '/src/integrations/upgrade-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\User_Profile_Integration' => __DIR__ . '/../..' . '/src/integrations/user-profile-integration.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Watchers\\Prominent_Words_Watcher' => __DIR__ . '/../..' . '/src/integrations/watchers/prominent-words-watcher.php',
        'Yoast\\WP\\SEO\\Premium\\Integrations\\Watchers\\Stale_Cornerstone_Content_Watcher' => __DIR__ . '/../..' . '/src/integrations/watchers/stale-cornerstone-content-watcher.php',
        'Yoast\\WP\\SEO\\Premium\\Introductions\\Application\\Ai_Fix_Assessments_Introduction' => __DIR__ . '/../..' . '/src/introductions/application/ai-fix-assessments-introduction.php',
        'Yoast\\WP\\SEO\\Premium\\Introductions\\Application\\Ai_Generate_Titles_And_Descriptions_Introduction' => __DIR__ . '/../..' . '/src/deprecated/introductions/application/ai-generate-titles-and-descriptions-introduction.php',
        'Yoast\\WP\\SEO\\Premium\\Main' => __DIR__ . '/../..' . '/src/main.php',
        'Yoast\\WP\\SEO\\Premium\\Presenters\\Icons\\Checkmark_Icon_Presenter' => __DIR__ . '/../..' . '/src/presenters/icons/checkmark-icon-presenter.php',
        'Yoast\\WP\\SEO\\Premium\\Presenters\\Icons\\Cross_Icon_Presenter' => __DIR__ . '/../..' . '/src/presenters/icons/cross-icon-presenter.php',
        'Yoast\\WP\\SEO\\Premium\\Presenters\\Icons\\Icon_Presenter' => __DIR__ . '/../..' . '/src/presenters/icons/icon-presenter.php',
        'Yoast\\WP\\SEO\\Premium\\Presenters\\Mastodon_Link_Presenter' => __DIR__ . '/../..' . '/src/presenters/mastodon-link-presenter.php',
        'Yoast\\WP\\SEO\\Premium\\Repositories\\Prominent_Words_Repository' => __DIR__ . '/../..' . '/src/repositories/prominent-words-repository.php',
        'Yoast\\WP\\SEO\\Premium\\Routes\\Link_Suggestions_Route' => __DIR__ . '/../..' . '/src/routes/link-suggestions-route.php',
        'Yoast\\WP\\SEO\\Premium\\Routes\\Prominent_Words_Route' => __DIR__ . '/../..' . '/src/routes/prominent-words-route.php',
        'Yoast\\WP\\SEO\\Premium\\Routes\\Workouts_Route' => __DIR__ . '/../..' . '/src/routes/workouts-route.php',
        'Yoast\\WP\\SEO\\Premium\\Surfaces\\Helpers_Surface' => __DIR__ . '/../..' . '/src/surfaces/helpers-surface.php',
        'Yoast\\WP\\SEO\\Premium\\User_Meta\\Framework\\Additional_Contactmethods\\Mastodon' => __DIR__ . '/../..' . '/src/user-meta/framework/additional-contactmethods/mastodon.php',
        'Yoast\\WP\\SEO\\Premium\\User_Meta\\User_Interface\\Additional_Contactmethods_Integration' => __DIR__ . '/../..' . '/src/user-meta/user-interface/additional-contactmethods-integration.php',
        'Yoast\\WP\\SEO\\Premium\\WordPress\\Wrapper' => __DIR__ . '/../..' . '/src/wordpress/wrapper.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf82072f1f0ebcd1562caedb68be83ef3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf82072f1f0ebcd1562caedb68be83ef3::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf82072f1f0ebcd1562caedb68be83ef3::$classMap;

        }, null, ClassLoader::class);
    }
}
