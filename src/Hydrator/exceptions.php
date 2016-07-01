<?php

namespace Milo\Hydrator
{
	class LogicException extends \LogicException
	{
	}

	abstract class HydrationException extends \RuntimeException
	{
	}

	class ExportException extends \RuntimeException
	{
	}

	class InvalidClassInstanceException extends HydrationException
	{
	}

	class MissingValueException extends HydrationException
	{
	}

	class InvalidValueException extends HydrationException
	{
	}

	abstract class BackendException extends HydrationException
	{
	}
}


namespace Milo\Hydrator\Backend
{
	use Milo\Hydrator\BackendException;

	class InvalidAnnotationException extends BackendException
	{
	}

	class InvalidClassException extends BackendException
	{
	}
}
