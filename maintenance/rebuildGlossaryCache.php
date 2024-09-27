<?php

namespace SG\Maintenance;

$basePath = getenv( 'MW_INSTALL_PATH' ) !== false ? getenv( 'MW_INSTALL_PATH' ) : __DIR__ . '/../../..';

require_once $basePath . '/maintenance/Maintenance.php';

/**
 * Rebuild glossary cache and update pages that contain a glossary term annotation
 *
 * Usage:
 * php rebuildGlossaryCache.php [options...]
 *
 * @ingroup SG
 * @ingroup SemanticGlossary
 * @ingroup Maintenance
 *
 * @license GPL-2.0-or-later
 * @since 1.1
 *
 * @author mwjames
 */
class RebuildGlossaryCache extends \Maintenance {

	public function __construct() {
		parent::__construct();

		$this->addDescription( "Rebuild glossary cache and update pages with glossary annotations." );
		$this->addDefaultParams();
	}

	/**
	 * @see Maintenance::addDefaultParams
	 */
	protected function addDefaultParams() {
		parent::addDefaultParams();
		$this->addOption( 'verbose', 'Be verbose about the progress', false, false, 'v' );
		$this->addOption( 'quiet', 'Do not give any output', false );
	}

	/**
	 * @see Maintenance::execute
	 */
	public function execute() {
		if ( !defined( 'SMW_VERSION' ) ) {
			$this->reportMessage( "You need to have SMW enabled in order to run the maintenance script!\n\n" );
			return false;
		}

		$this->reportMessage( "This script is not yet finished with the latest version of Lingo!\n\n" );

		/*$glossaryCacheRebuilder = new GlossaryCacheRebuilder(
			StoreFactory::getStore(),
			new GlossaryCache(),
			array( $this, 'reportMessage' )
		);

		$glossaryCacheRebuilder->setParameters( $this->mOptions );

		if ( $glossaryCacheRebuilder->rebuild() ) {
			return true;
		}

		$this->reportMessage( $this->mDescription . "\n\n" . 'Use option --help for details.' . "\n"  );
		*/
		return false;
	}

	/**
	 * @since 1.1
	 *
	 * @param string $message
	 */
	public function reportMessage( $message ) {
		$this->output( $message );
	}

}

$maintClass = 'SG\Maintenance\RebuildGlossaryCache';
require_once RUN_MAINTENANCE_IF_MAIN;
