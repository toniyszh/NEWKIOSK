DECLARE @TBL TABLE (
    [name] nvarchar(500),
    [rows] bigint,
    [reserved] nvarchar(500),
    [data] nvarchar(500),
    [index_size] nvarchar(500),
    [unused] nvarchar(500)
)

INSERT INTO @TBL
EXEC sp_MSforeachtable @command1="EXEC sp_spaceused '?'"

--SELECT *,'SELECT ''' + name + ''', COUNT(*) FROM [' + name + '] UNION ALL', 'DELETE FROM [' + name + '];', 'DBCC CHECKIDENT(''[' + name + ']'',RESEED,0);','SELECT * FROM ' + name FROM @TBL 
SELECT 'DELETE FROM [' + name + ']; DBCC CHECKIDENT(''[' + name + ']'',RESEED,0);', *, 'SELECT * FROM [' + name + ']', 'DROP TABLE [' + name + ']'
FROM @TBL
--WHERE name LIKE '%Sync%' AND name NOT IN ('Sync_StockLogs_OLD_20170203', 'xSync_Series', 'Sync_PurchaseOrderEntries_BACKUP')
--ORDER BY name asc
--WHERE name LIKE '%Sync%' AND [rows] > 0
WHERE [rows] > 0
    and name NOT IN ('Country', 'BundledItems', 'BundledItemsOptions', 'NewBundleComponents', 'ItemMatrix', 'BundleList','BundledItems', 'Category', 'Concessionaire', 'Configuration', 'Customer', 'Department', 'Item', 'Municipalities', 'POSSecurity', 'ReasonCode', 'ReceiptHeader','Reading_Header', 'ReportFilterFields', 'ReportName', 'ReportPriveleges', 'Reports', 'SecurityManager', 'Store', 'SubCategory', 'Supplier', 'SupplierList', 'TaxRate', 'TenderTypes', 'Users', 'SalesRepresentative')
    and name NOT IN ('AllCardInfo', 'AllCardInfo_OLD', 'AllCardInfoMID', 'Bank', 'CreditCardTerms', 'Freebies', 'ItemContent')
    and name NOT IN ('TM_Areas', 'TM_Tables', 'License', 'Integration_Location', 'Integration_LocationDetails', 'KDSArea', 'KDSItemAssignment', 'WristbandRestriction')
    and name NOT IN ('AddOnControls', 'InventoryLocations', 'ItemFlag', 'ItemPrinter', 'POSFailOverPrinter')
    and name NOT IN ('Inventory', 'POSAccessCodes', 'Pricing', 'Store_Configuration', 'StoreReportPriveleges', 'StoreReports', 'StoreSecurityManager')
    and name NOT IN ('AllCardInfo_20200309', 'AllCardInfo_OLD_2', 'CameraHaus_ImportItems', 'ItemAddCol')
    and name NOT IN ('CustomerTagging', 'ItemPricing', 'SalesEffectivity')
    --NE
    and name NOT IN ('Cloud_Counter', 'Cloud_Tables', 'Membership','xxxxxxxxxx')
    --ANJO
    and name NOT IN ('DepartmentSchedule', 'ItemScheduledPricing', 'ItemUpgradeLimit', 'QRCode', 'QRCodeSKU', 'QRCodeSKUMapping', 'StructureAsAccess', 'ItemQROnly')
    --WATER WORLD
    and name NOT IN ('RFIDRestriction', 'WeeDoo_TicketDetails', 'WeeDoo_LastDownloadID', 'Integration_Reg_Location')
    --DUNKIN
    and name NOT IN ('CreditCardMerchant', 'CustomerType', 'DeliveryCharge', 'DeliveryType', 'DeliveryTypeAccess', 'DoublePrintFilter', 'ReceiptQRConfig')
    and name NOT IN ('Item_NonFood', 'ItemList', 'ItemSCLessVat', 'KDSRegisterName', 'Modifiers', 'TenderTypesPercentage','TenderTypeAccess')
    and name NOT IN ('ItemDoNotShowFilter', 'ReceiptQR', 'CashDrawerLimit')
    and name NOT IN ('AdvancePaymentTenderList')
    --FILA
    and name NOT IN ('Store_SAP','MonthFullName', 'Brand', 'Color', 'Size', 'Style')
    --STARCITY
    and name NOT IN ('ItemTurnstileMapping','POSSecurityAddOns','Cashiers', 'Customer_20190830','Customer_20190830_backup', 'POSMachines')
    --WEEDOO
    and name NOT IN ('wd_itemlist'
)--SHAKEYS
    and name NOT IN ('ItemSchedule','Zeus_Product_Options','Zeus_Product_Options_Values','Zeus_Product_Options_Values2','Zeus_Products_Service_Channels','Zeus_Promotions')
    and name NOT IN ('ItemsDiscountExempted','RequireSuperCard')
    --RETAILWARE UPDATE
    and name NOT IN ('ClientAppVersion')
    --USOH
    and name NOT IN ('ItemUOM')
    --SHG KIOSK
    and name NOT IN ('ReasonCodeKiosk','KIOSK_Category')
    --SHG
    and name NOT IN ('TM_ColorFlag', 'TM_TableColor', 'TM_AreaOrderTypeMapping', 'TM_TableGroupType')
    --SHG HQ
    and name NOT IN ('Customer_Bak_20230323', 'Department_Mapping', 'ItemAssignment', 
'ItemHistory', 'PriceUpdateLogs', 'ReasonCode_Mapping', 'Store_Mapping', 
'store_portaluser', 'storeextension', 'storegroup', 'TM_Areas_Mapping', 
'UserRecordLogs', 'ItemModifier', 'ItemSpecialInstruction', 'OrderTypeSelection', 'ItemPrinterSorting')
 --WYW 
 and name NOT IN ('ConfigurationAddOns','RecomputeRegisterKiosk','RegisterServiceCharge_Kiosk','ServiceChargeRegister_Kiosk','KIOSK_TransactionItem_SpecialInstruction','TransactionHoldEntriesToRegisterLink1','TransactionHoldEntriesToRegisterLink2')
 --general
 and name NOT IN ('AccreditationDetails')

ORDER BY name ASC
--update QRCode set POSStatus = null, LastUpdated = null, TransactionNo = null, RegisterNo = null
/**

SELECT * FROM Users ORDER BY ID

UPDATE Users SET Username = '', Password = '' WHERE ID = 99999
UPDATE Item SET Quantity = 100


--SHG
SELECT* FROM TM_Tables WHERE Name LIKE '%,%'
SELECT* FROM TM_TableColor WHERE TableName LIKE '%,%'

UPDATE a SET a.TableName = b.OriginalName FROM TM_TableColor a JOIN TM_Tables_Joined b ON a.TableID = b.TableID
UPDATE a SET a.Name = b.OriginalName FROM TM_Tables a JOIN TM_Tables_Joined b ON a.ID = b.TableID
UPDATE TM_TableColor SET GroupType = '', ColorID = (SELECT ID FROM TM_ColorFlag WHERE Code = 'TS0001')

**/