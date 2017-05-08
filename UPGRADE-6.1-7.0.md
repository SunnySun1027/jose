# Upgrade from 6.1 to 7.0

## Features added

* No feature added

## Deprecation removed

* `Jose\Algorithm\JWAManager::isAlgorithmSupported` renamed to `Jose\Algorithm\JWAManager::has`
* `Jose\Algorithm\JWAManager::getAlgorithms` renamed to `Jose\Algorithm\JWAManager::all`
* `Jose\Algorithm\JWAManager::listAlgorithms` renamed to `Jose\Algorithm\JWAManager::list`
* `Jose\Algorithm\JWAManager::getAlgorithm` renamed to `Jose\Algorithm\JWAManager::get`
* `Jose\Algorithm\JWAManager::addAlgorithm` renamed to `Jose\Algorithm\JWAManager::add`
* `Jose\Algorithm\JWAManagerInterface` removed
* `Jose\VerifierInterface` removed
* `Jose\SignerInterface` removed
* `Jose\DecrypterInterface` removed
* `Jose\EncdrypterInterface` removed
* `Jose\JWTCreatorInterface` removed
* `Jose\JWTLoaderInterface` removed
* `Jose\Object\JWSInterface` removed
* `Jose\Object\JWEInterface` removed
* `Jose\Object\RecipientInterface` removed
* `Jose\Object\SignatureInterface` removed
* `Jose\Factory\JWKFactoryInterface` removed
* `Jose\Factory\JWSFactoryInterface` removed
* `Jose\Compression\CompressionManagerInterface` removed