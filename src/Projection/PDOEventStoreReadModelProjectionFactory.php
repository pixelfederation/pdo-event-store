<?php
/**
 * This file is part of the prooph/pdo-event-store.
 * (c) 2016-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\EventStore\PDO\Projection;

use PDO;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\PDO\Exception;
use Prooph\EventStore\PDO\Projection\ProjectionOptions as PDOProjectionOptions;
use Prooph\EventStore\Projection\ProjectionOptions;
use Prooph\EventStore\Projection\ReadModel;
use Prooph\EventStore\Projection\ReadModelProjection;
use Prooph\EventStore\Projection\ReadModelProjectionFactory;

final class PDOEventStoreReadModelProjectionFactory implements ReadModelProjectionFactory
{
    /**
     * @var PDO
     */
    private $connection;

    /**
     * @var string
     */
    private $eventStreamsTable;

    public function __construct(PDO $connection, string $eventStreamsTable)
    {
        $this->connection = $connection;
        $this->eventStreamsTable = $eventStreamsTable;
    }

    public function __invoke(
        EventStore $eventStore,
        string $name,
        ReadModel $readModel,
        ProjectionOptions $options = null
    ): ReadModelProjection {
        if (null === $options) {
            $options = new PDOProjectionOptions();
        }

        if (! $options instanceof PDOProjectionOptions) {
            throw new Exception\InvalidArgumentException(
                self::class . ' expects an instance of' . PDOProjectionOptions::class
            );
        }

        return new PDOEventStoreReadModelProjection(
            $eventStore,
            $this->connection,
            $name,
            $readModel,
            $this->eventStreamsTable,
            $options->projectionsTable(),
            $options->lockTimeoutMs(),
            $options->persistBlockSize(),
            $options->sleep()
        );
    }
}
