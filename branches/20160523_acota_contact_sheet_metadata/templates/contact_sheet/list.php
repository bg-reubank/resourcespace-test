<style>
<?php
if(isset($refnumberfontsize))
    {
    ?>
    page { font-size: <?php echo $refnumberfontsize; ?>px; }
    <?php
    }

if(isset($titlefontsize))
    {
    ?>
    #pageTitle { font-size: <?php echo $titlefontsize; ?>px; }
    <?php
    }
    ?>

#logo { height: 50px; max-width: 100%; }
.centeredText { text-align: center; }

#resourcesTable { width: 100%; vertical-align: top; }
.resourcePreviewContainer { width: 30%; padding-bottom: 20px; }
.resourceDataContainer { width: 70%; padding-bottom: 20px; }
.resourcePreview { width: <?php echo $column_width; ?>px; }
</style>
<page backtop="25mm" backbottom="25mm">
<?php
if(isset($contactsheet_header))
    {
    ?>
    <page_header>
        <table cellspacing="0" style="width: 100%;">
            <tr>
                <td style="width: 60%;"><h1><?php echo $applicationname; ?></h1></td>
            <?php
            if(isset($add_contactsheet_logo))
                {
                ?>
                <td style="width: 40%;" align=right>
                    <img id="logo" src="<?php echo $contact_sheet_logo; ?>" alt="Logo">
                </td>
                <?php
                }
                ?>
            </tr>
        </table>
        <hr>
    </page_header>
    <?php
    }

if(isset($contact_sheet_footer))
    {
    ?>
    <page_footer>
        <hr>
        <table style="width: 100%;">
            <tr>
                <td class="centeredText" style="width: 90%">
                    <span><?php echo $lang['contact_sheet_footer_address']; ?></span>
                    <p><?php echo $lang['contact_sheet_footer_copyright']; ?></p>
                </td>
                <td style="text-align: right; width: 10%">[[page_cu]] of [[page_nb]]</td>
            </tr>
        </table>
    </page_footer>
    <?php
    }
    ?>


    <!-- Real content starts here -->
    <h3 id="pageTitle"><?php echo $title; ?></h3>
    <table id="resourcesTable">
        <tbody>
        <?php
        foreach($resources as $resource_ref => $resource)
            {
            ?>
            <tr>
                <td class="resourcePreviewContainer">
                <?php
                if(isset($contact_sheet_add_link))
                    {
                    // IMPORTANT: having space between a tag and img creates some weird visual lines (HTML2PDF issues maybe?!)
                    ?>
                    <a target="_blank" href="<?php echo $baseurl; ?>/?r=<?php echo $resource_ref; ?>"><img class="resourcePreview" src="<?php echo $resource['preview_src']; ?>" alt="Resource Preview"></a>
                    <?php
                    }
                else
                    {
                    ?>
                    <img class="resourcePreview" src="<?php echo $resource['preview_src']; ?>" alt="Resource Preview">
                    <?php
                    }
                    ?>
                </td>
                <td class="resourceDataContainer">
                <?php
                if($config_sheetthumb_include_ref)
                    {
                    ?>
                    <span class="resourceRef"><?php echo $resource_ref; ?></span><br>
                    <?php
                    }

                foreach($resource['contact_sheet_fields'] as $contact_sheet_field)
                    {
                    ?>
                    <span><?php echo $contact_sheet_field; ?></span><br>
                    <?php
                    }
                    ?>
                </td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>

</page>