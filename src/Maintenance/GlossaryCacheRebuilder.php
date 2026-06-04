<?php

namespace SG\Maintenance;

use Lingo\LingoParser;
use SG\Cache\GlossaryCache;
use SG\PropertyRegistrationHelper;
use SMW\DataItems\Property;
use SMW\MediaWiki\JobFactory;
use SMW\Query\Language\SomeProperty;
use SMW\Query\Language\ThingDescription;
use SMW\Query\Query;
use SMW\Query\QueryResult;
use SMW\Store;

/**
 * Part of the `rebuildGlossaryCache.php` maintenance script
 *
 * @ingroup SG
 *
 * @license GPL-2.0-or-later
 * @since 1.1
 *
 * @author mwjames
 */
class GlossaryCacheRebuilder {

	/** @var Store */
	private $store;

	/** @var GlossaryCache */
	private $glossaryCache;

	/** @var JobFactory */
	private $jobFactory;

	/**
	 * @var null
	 */
	private $reporter = null;

	/**
	 * @var int
	 */
	private $rebuildCount = 0;

	/**
	 * @var bool
	 */
	private $verbose = false;

	/**
	 * @since 1.1
	 *
	 * @param Store $store
	 * @param GlossaryCache $glossaryCache
	 * @param JobFactory $jobFactory
	 * @param null $reporter
	 *
	 * @return void
	 */
	public function __construct(
		Store $store,
		GlossaryCache $glossaryCache,
		JobFactory $jobFactory,
		$reporter = null
	) {
		$this->store = $store;
		$this->glossaryCache = $glossaryCache;
		$this->jobFactory = $jobFactory;

		// Should be a MessageReporter instance
		$this->reporter = $reporter;
	}

	/**
	 * @since 1.1
	 *
	 * @param array $options
	 */
	public function setParameters( array $options ) {
		$this->verbose = array_key_exists( 'verbose', $options );
	}

	/**
	 * @since 1.1
	 *
	 * @return int
	 */
	public function getRebuildCount() {
		return $this->rebuildCount;
	}

	/**
	 * @since 1.1
	 *
	 * @return bool
	 */
	public function rebuild() {
		$pages = $this->store->getQueryResult( $this->buildQuery() )->getResults();

		$this->removeEntitiesFromCache( $pages );
		$this->updateSelectedPages( $pages );

		return true;
	}

	/**
	 * Update selected pages
	 *
	 * @param array $pages
	 *
	 * @return true
	 */
	private function updateSelectedPages( array $pages ) {
		$titleCache = [];

		foreach ( $pages as $page ) {

			$title = $page->getTitle();

			if ( $title !== null && !isset( $titleCache[ $title->getPrefixedDBkey() ] ) ) {

				$this->rebuildCount++;

				$this->reportMessage(
					"($this->rebuildCount) Processing page " . $title->getPrefixedDBkey() . " ...\n",
					$this->verbose
				);

				$updatejob = $this->jobFactory->newUpdateJob( $title );
				$updatejob->run();

				$titleCache[ $title->getPrefixedDBkey() ] = true;
			}
		}

		$this->reportMessage( "$this->rebuildCount pages refreshed.\n" );

		return true;
	}

	/**
	 * Build a query to retrieve all pages that have a glossary term
	 *
	 * @return Query
	 */
	private function buildQuery() {
		$description = new SomeProperty(
			new Property( PropertyRegistrationHelper::SG_TERM ),
			new ThingDescription()
		);

		$countQuery = new Query( $description, false, false );
		$countQuery->querymode = Query::MODE_COUNT;

		$queryResult = $this->store->getQueryResult( $countQuery );
		$numberOfPages = $queryResult instanceof QueryResult ? $queryResult->getCountValue() : $queryResult;

		$resultQuery = new Query(
			$description,
			false,
			false
		);

		$resultQuery->setUnboundLimit( $numberOfPages, false );

		return $resultQuery;
	}

	/**
	 * Remove entities from cache
	 *
	 * @param array $pages
	 *
	 * @return void
	 */
	private function removeEntitiesFromCache( array $pages ) {
		$cache = $this->glossaryCache->getCache();

		LingoParser::purgeCache();

		foreach ( $pages as $page ) {
			$cache->delete( $this->glossaryCache->getKeyForSubject( $page ) );
		}

		$this->reportMessage( "\n" . ( count( $pages ) + 1 ) . " cache entities deleted.\n\n" );
	}

	/**
	 * @param string $message
	 * @param bool $output
	 *
	 * @return void
	 */
	private function reportMessage( string $message, $output = true ) {
		if ( is_callable( $this->reporter ) && $output ) {
			call_user_func( $this->reporter, $message );
		}
	}

}
