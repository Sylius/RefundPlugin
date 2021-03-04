# CHANGELOG

### v1.0.0-RC.7 (2021-03-04)

### Added
- [#261](https://github.com/Sylius/RefundPlugin/issues/261) Update GitHub Actions to run on Sylius 1.9 instead of 1.9-beta ([@GSadee](https://github.com/GSadee))

### Fixed
- [#260](https://github.com/Sylius/RefundPlugin/issues/260) Fix OrderItemsTaxesApplicator with removed units ([@aleho](https://github.com/aleho))

### v1.0.0-RC.6 (2021-02-18)

### Added
- [#242](https://github.com/Sylius/RefundPlugin/issues/242) Refunding shipments with taxes ([@GSadee](https://github.com/GSadee))
- [#255](https://github.com/Sylius/RefundPlugin/issues/255) Refund shipping cost with promotion applied ([@GSadee](https://github.com/GSadee))
- [#254](https://github.com/Sylius/RefundPlugin/issues/254) Allow for Sylius 1.8 & 1.9 and Symfony 4.4 & 5.2 ([@pamil](https://github.com/pamil), [@GSadee](https://github.com/GSadee))

### Changed
- [#238](https://github.com/Sylius/RefundPlugin/issues/238) [Maintenance] Remove security checker config ([@clem21](https://github.com/clem21))
- [#217](https://github.com/Sylius/RefundPlugin/issues/217) Add missing french translations ([@Nek-](https://github.com/Nek-))
- [#209](https://github.com/Sylius/RefundPlugin/issues/209) Fix wrong type error if the error in some cases ([@Nek-](https://github.com/Nek-))
- [#243](https://github.com/Sylius/RefundPlugin/issues/243) [Minor] Clean up behat scenarios ([@clem21](https://github.com/clem21))
- [#244](https://github.com/Sylius/RefundPlugin/issues/244) [Maintenance] Drop Sylius 1.7 support ([@GSadee](https://github.com/GSadee))
- [#245](https://github.com/Sylius/RefundPlugin/issues/245) Remove final tags from resource entities ([@GSadee](https://github.com/GSadee))
- [#248](https://github.com/Sylius/RefundPlugin/issues/248) [Shipment] Improve interface inheritance ([@GSadee](https://github.com/GSadee))
- [#250](https://github.com/Sylius/RefundPlugin/issues/250) [Pdf] set translation credit memo locale ([@SirDomin](https://github.com/SirDomin))
- [#256](https://github.com/Sylius/RefundPlugin/issues/256) Make OrderShipmentTaxesApplicator final service again ([@GSadee](https://github.com/GSadee))
- [#257](https://github.com/Sylius/RefundPlugin/issues/257) [Adjustment] Update down methods of duplicated migrations not to execute if the changes already exist in db ([@GSadee](https://github.com/GSadee))

### Fixed
- [#236](https://github.com/Sylius/RefundPlugin/issues/236) fix doctrine migration sql ([@arti0090](https://github.com/arti0090))
- [#234](https://github.com/Sylius/RefundPlugin/issues/234) [UI/UX] Refund float price ([@TheGrimmChester](https://github.com/TheGrimmChester))
- [#216](https://github.com/Sylius/RefundPlugin/issues/216) Refund list view fails if CreditMemo entity has been overridden ([@luca-rath](https://github.com/luca-rath))
- [#240](https://github.com/Sylius/RefundPlugin/issues/240) [BUG] Credit memos and refund payment appears in different order ([@clem21](https://github.com/clem21))
- [#251](https://github.com/Sylius/RefundPlugin/issues/251) Fix failing build ([@SirDomin](https://github.com/SirDomin))

### v1.0.0-RC.5 (2020-11-30)

### Changed
- [#227](https://github.com/Sylius/RefundPlugin/issues/227) Switch from Travis to GitHub Actions ([@pamil](https://github.com/pamil))
- [#230](https://github.com/Sylius/RefundPlugin/issues/230) Support Sylius 1.7 again ([@Zales0123](https://github.com/Zales0123))

### Fixed
- [#228](https://github.com/Sylius/RefundPlugin/issues/228) Fix Doctrine/Migrations configuration ([@Zales0123](https://github.com/Zales0123))

### v1.0.0-RC.4 (2020-10-22)

### Changed
- [#205](https://github.com/Sylius/RefundPlugin/issues/205) Switch to Doctrine/Migrations 3.0 ([@pamil](https://github.com/pamil), [@GSadee](https://github.com/GSadee))
- [#222](https://github.com/Sylius/RefundPlugin/issues/222) Add check for not sending emails when refund validation fails ([@GSadee](https://github.com/GSadee))
- [#224](https://github.com/Sylius/RefundPlugin/issues/224) [Docs] Update README and UPGRADE files ([@lchrusciel](https://github.com/lchrusciel))

### Fixed
- [#223](https://github.com/Sylius/RefundPlugin/issues/223) Making the RefundFactory implement the factory interface ([@mamazu](https://github.com/mamazu))

### v1.0.0-RC.3 (2020-04-28)

### Added
- [#195](https://github.com/Sylius/RefundPlugin/issues/195) feat: add french translation ([@Gregcop1](https://github.com/Gregcop1))

### Changed
- [#198](https://github.com/Sylius/RefundPlugin/issues/198) Minor restyle of order credit memo list ui ([@diimpp](https://github.com/diimpp))
- [#197](https://github.com/Sylius/RefundPlugin/issues/197) Convert RefundPayment state values to lowercase ([@diimpp](https://github.com/diimpp))
- [#200](https://github.com/Sylius/RefundPlugin/issues/200) Misc refactor ([@diimpp](https://github.com/diimpp))
- [#202](https://github.com/Sylius/RefundPlugin/issues/202) [DB] Remove `COMMENT` database configuration ([@lchrusciel](https://github.com/lchrusciel))
- [#206](https://github.com/Sylius/RefundPlugin/issues/206) [Doc] Update UPGRADE file ([@lchrusciel](https://github.com/lchrusciel))

### Fixed
- [#199](https://github.com/Sylius/RefundPlugin/issues/199) Fix getId/id return typehints for entities ([@diimpp](https://github.com/diimpp))
- [#203](https://github.com/Sylius/RefundPlugin/issues/203) Fix 'Unknown "locale" filter' error ([@dunglas](https://github.com/dunglas))
- [#204](https://github.com/Sylius/RefundPlugin/issues/204) Migrations: fix compatibility with MySQL 8 ([@dunglas](https://github.com/dunglas))

### v1.0.0-RC.2 (2020-01-31)

- [#177](https://github.com/Sylius/RefundPlugin/issues/177) Update README with refunds payment information ([@AdamKasp](https://github.com/AdamKasp))
- [#179](https://github.com/Sylius/RefundPlugin/issues/179) [CreditMemo] Rework credit memo ([@GSadee](https://github.com/GSadee))
- [#182](https://github.com/Sylius/RefundPlugin/issues/182) [TaxItem] Make tax item a resource ([@GSadee](https://github.com/GSadee))
- [#181](https://github.com/Sylius/RefundPlugin/issues/181) Extract tax rate provider from line items converter ([@GSadee](https://github.com/GSadee))
- [#180](https://github.com/Sylius/RefundPlugin/issues/180) [Refunds] Disable refund when order is free ([@AdamKasp](https://github.com/AdamKasp))
- [#183](https://github.com/Sylius/RefundPlugin/issues/183) Code standards fixes ([@AdamKasp](https://github.com/AdamKasp))

### v1.0.0-RC.1 (2020-01-15)

- [#172](https://github.com/Sylius/RefundPlugin/issues/172) Navigation via breadcrumbs between credit memo - Order and refund page - order ([@Zales0123](https://github.com/Zales0123), [@AdamKasp](https://github.com/AdamKasp))
- [#174](https://github.com/Sylius/RefundPlugin/issues/174) [CreditMemo] Remove unused service from credit memo generator ([@GSadee](https://github.com/GSadee))
- [#173](https://github.com/Sylius/RefundPlugin/issues/173) [CreditMemo] Add tax items to credit memo ([@GSadee](https://github.com/GSadee))
- [#175](https://github.com/Sylius/RefundPlugin/issues/175) Configuring supported gateways instead of hardcoding them ([@Zales0123](https://github.com/Zales0123))

### v0.10.1 (2019-12-18)

- [#170](https://github.com/Sylius/RefundPlugin/issues/170) Various fixes including doctrine mapping exception ([@loevgaard](https://github.com/loevgaard))

### v0.10.0 (2019-12-06)

- [#150](https://github.com/Sylius/RefundPlugin/issues/150) [DX][Twig] Extract even more templates ([@Zales0123](https://github.com/Zales0123))
- [#162](https://github.com/Sylius/RefundPlugin/issues/162) Change server to new symfony server ([@AdamKasp](https://github.com/AdamKasp))
- [#167](https://github.com/Sylius/RefundPlugin/issues/167) [Refund] Do not expose exception messages to the customer ([@lchrusciel](https://github.com/lchrusciel), [@Zales0123](https://github.com/Zales0123))
- [#168](https://github.com/Sylius/RefundPlugin/issues/168) Allow for Messenger 4.3 and 4.4 ([@pamil](https://github.com/pamil))
