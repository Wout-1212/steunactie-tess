// Panel view for the Donaties area (data from lib/panel.php). The Panel ships Vue with its template
// compiler, so this plain file needs no build step. Filters, search and paging live in the URL query.
panel.plugin("tess/donations", {
  components: {
    "k-donations-view": {
      props: {
        enabled: Boolean,
        testMode: Boolean,
        filter: String,
        filters: Array,
        search: String,
        pagination: Object,
        rows: Array,
      },
      data() {
        return { query: this.search };
      },
      computed: {
        columns() {
          return {
            date: { label: "Datum", width: "9rem", mobile: true },
            name: { label: "Naam", mobile: true },
            amount: { label: "Bedrag", width: "8rem", align: "right", mobile: true },
            message: { label: "Bericht" },
            wall: { label: "Op de site", width: "9rem" },
            payment: { label: "Betaling", width: "8rem" },
            mail: { label: "Bedankmail", width: "8rem" },
          };
        },
      },
      methods: {
        go(query) {
          const next = { filter: this.filter, q: this.search, page: 1, ...query };
          this.$go("donations", { query: next });
        },
        onSearch() {
          clearTimeout(this.searchTimer);
          this.searchTimer = setTimeout(() => this.go({ q: this.query }), 400);
        },
        async onOption(option, row) {
          if (option === "edit") {
            return this.$panel.dialog.open(`donations/${row.id}/edit`);
          }
          try {
            const result = await this.$api.post(`tess/donations/${row.id}/${option}`);
            this.$panel.notification.success(result.message);
            this.$panel.view.reload();
          } catch (error) {
            this.$panel.notification.error(error);
          }
        },
      },
      template: `
        <k-panel-inside class="k-donations-view">
          <k-header>Donaties</k-header>
          <k-box v-if="!enabled" theme="info" icon="info" style="margin-bottom: var(--spacing-6)"
            text="Online doneren via Mollie staat uit: er is nog geen Mollie-sleutel ingesteld in de serverconfiguratie." />
          <k-box v-if="testMode" theme="notice" icon="alert" style="margin-bottom: var(--spacing-6)"
            text="TESTMODUS: dit zijn testbetalingen met een Mollie-testsleutel. Er is geen echt geld betaald." />
          <div style="display: flex; flex-wrap: wrap; gap: var(--spacing-3); align-items: center; margin-bottom: var(--spacing-6)">
            <k-button-group>
              <k-button v-for="item in filters" :key="item.value" :text="item.text" size="sm"
                :variant="item.value === filter ? 'filled' : 'dimmed'" @click="go({ filter: item.value })" />
            </k-button-group>
            <k-input v-model="query" type="text" icon="search" placeholder="Zoek op naam, e-mail of bericht"
              style="margin-left: auto; min-width: 16rem" @input="onSearch" />
          </div>
          <k-table :columns="columns" :rows="rows" :index="false" :pagination="pagination"
            empty="Geen donaties gevonden." @paginate="go({ page: $event.page })" @option="onOption" />
        </k-panel-inside>
      `,
    },
  },
});
