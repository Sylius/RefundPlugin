# CHANGELOG

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
