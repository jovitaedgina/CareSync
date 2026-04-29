<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/marketplace_helpers.php';

requireLogin();

$pageTitle = 'Apotek Digital - CareSync';
$currentPage = 'marketplace';
$userId = (int) (currentUser()['id'] ?? 0);
$categories = getMarketplaceCategories();
$products = getMarketplaceProducts($pdo);

$extraHead = '
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: [\'"Plus Jakarta Sans"\', \'sans-serif\'],
                },
                colors: {
                    primary: \'#1D4ED8\',
                    primaryLight: \'#EFF6FF\',
                    accent: \'#10B981\',
                    dark: \'#0F172A\',
                    textSoft: \'#64748B\'
                },
                boxShadow: {
                    floating: \'0 20px 40px -15px rgba(29, 78, 216, 0.25)\',
                }
            }
        }
    }
</script>
<style>
    .smooth-transition { transition: all 0.3s ease-in-out; }
    body { background-color: #F8FAFC; margin: 0; }
</style>
';

include __DIR__ . '/../includes/header.php';
?>

<div class="bg-slate-50 min-h-screen py-10" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-primary to-blue-500 rounded-[2rem] p-8 md:p-10 mb-10 shadow-floating flex flex-col md:flex-row items-center justify-between relative overflow-hidden group">
            <i class="fa-solid fa-file-prescription text-white/10 text-9xl absolute -right-4 -bottom-8 group-hover:scale-110 group-hover:-rotate-12 smooth-transition duration-500"></i>
            <div class="relative z-10 text-white md:w-2/3">
                <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm border border-white/30 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-4">
                    <i class="fa-solid fa-camera"></i> Layanan Praktis
                </div>
                <h2 class="text-3xl md:text-4xl font-extrabold mb-3 leading-tight text-white m-0">Marketplace Obat & Apotek CareSync</h2>
                <p class="text-blue-100 text-lg mb-8 max-w-2xl leading-relaxed mt-2">Obat Keras hanya bisa dibeli jika disertai resep dokter.</p>
                <a href="<?= BASE_URL ?>/pages/prescription.php" class="bg-white text-primary px-8 py-3.5 rounded-full font-bold shadow-lg hover:bg-slate-50 hover:-translate-y-1 active:scale-95 smooth-transition flex items-center gap-2 border-none cursor-pointer no-underline w-fit">
                    <i class="fa-solid fa-file-signature"></i> Lihat E-Resep
                </a>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <div class="lg:w-1/4">
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm sticky top-28">
                    <h3 class="font-extrabold text-lg text-slate-900 mb-4 m-0">Kategori Obat</h3>
                    <div class="flex flex-col gap-2 mt-4">
                        <?php foreach ($categories as $index => $cat): ?>
                        <button onclick="filterCategory('<?= addslashes($cat['name']) ?>')" class="cat-btn flex items-center gap-3 px-4 py-3 rounded-2xl smooth-transition font-bold text-sm w-full text-left border-none cursor-pointer <?= $index === 0 ? 'bg-primary text-white shadow-md shadow-blue-200' : 'bg-transparent text-slate-600 hover:bg-slate-50 hover:text-primary' ?>" data-cat="<?= htmlspecialchars($cat['name']) ?>">
                            <i class="fa-solid <?= $cat['icon'] ?> w-5 text-center"></i>
                            <?= htmlspecialchars($cat['name']) ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="lg:w-3/4">
                <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-sm flex flex-col sm:flex-row gap-4 justify-between items-center mb-8">
                    <div class="relative w-full sm:w-2/3">
                        <i class="fa-solid fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input id="market-search" type="text" placeholder="Cari nama obat, vitamin, atau suplemen..." class="w-full bg-slate-50 border border-slate-200 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primaryLight rounded-2xl py-3 pl-12 pr-4 text-sm font-semibold text-slate-700 outline-none smooth-transition">
                    </div>
                    <div class="w-full sm:w-auto flex gap-2">
                        <button type="button" onclick="sortProducts('name')" class="flex-1 sm:flex-none bg-slate-50 hover:bg-slate-100 text-slate-600 px-5 py-3 rounded-2xl font-bold text-sm border border-slate-200 smooth-transition flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-arrow-down-a-z"></i> Nama
                        </button>
                        <button type="button" onclick="sortProducts('price')" class="flex-1 sm:flex-none bg-slate-50 hover:bg-slate-100 text-slate-600 px-5 py-3 rounded-2xl font-bold text-sm border border-slate-200 smooth-transition flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-tags"></i> Harga
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6" id="product-grid">
                    <?php foreach ($products as $product): ?>
                    <?php
                        $prescriptionLocked = $product['requires_prescription'] && !userHasPrescriptionForProduct($pdo, $userId, $product);
                    ?>
                    <div
                        class="product-card bg-white rounded-3xl border border-slate-100 shadow-sm hover:-translate-y-2 hover:shadow-floating group flex flex-col smooth-transition relative overflow-hidden"
                        data-category="<?= htmlspecialchars($product['category']) ?>"
                        data-name="<?= htmlspecialchars(strtolower($product['name'])) ?>"
                        data-price="<?= (int) $product['price'] ?>"
                    >
                        <a href="<?= BASE_URL ?>/pages/product-detail.php?id=<?= (int) $product['id'] ?>" class="block p-5 pb-0 cursor-pointer no-underline flex-1">
                            <div class="bg-white rounded-2xl h-48 flex items-center justify-center mb-4 group-hover:scale-105 smooth-transition overflow-hidden relative p-2">
                                <img src="<?= htmlspecialchars($product['img']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="object-contain mix-blend-multiply h-full w-full group-hover:scale-110 smooth-transition">
                            </div>

                            <span class="text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-md w-fit mb-3 block <?= htmlspecialchars($product['badge']) ?>">
                                <?= htmlspecialchars($product['category']) ?>
                            </span>

                            <?php if ($product['requires_prescription']): ?>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-md w-fit mb-3 block bg-red-50 text-red-600 border border-red-100">
                                Wajib Resep Dokter
                            </span>
                            <?php endif; ?>

                            <h4 class="font-extrabold text-slate-900 text-[15px] leading-snug mb-2 group-hover:text-primary smooth-transition line-clamp-2 m-0" title="<?= htmlspecialchars($product['name']) ?>">
                                <?= htmlspecialchars($product['name']) ?>
                            </h4>

                            <div class="flex items-center gap-3 mb-2 text-xs font-bold text-slate-500 mt-2">
                                <span class="flex items-center gap-1 text-amber-500"><i class="fa-solid fa-star"></i> <?= htmlspecialchars($product['rating']) ?></span>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                <span>Terjual <?= htmlspecialchars($product['sold']) ?></span>
                            </div>
                        </a>

                        <div class="p-5 pt-3 mt-auto border-t border-slate-50 flex items-center justify-between gap-3">
                            <div class="font-extrabold text-primary text-lg text-slate-900 m-0">Rp <?= number_format($product['price'], 0, ',', '.') ?></div>
                            <button
                                type="button"
                                onclick="addToCartDirect(<?= (int) $product['id'] ?>, '<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>')"
                                class="w-11 h-11 rounded-xl <?= $prescriptionLocked ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-primaryLight text-primary hover:bg-primary hover:text-white cursor-pointer' ?> flex items-center justify-center active:scale-95 smooth-transition border-none shadow-sm"
                                title="<?= $prescriptionLocked ? 'Produk resep membutuhkan resep dokter' : 'Tambah ke Keranjang' ?>"
                                <?= $prescriptionLocked ? 'disabled' : '' ?>
                            >
                                <i class="fa-solid <?= $prescriptionLocked ? 'fa-file-signature' : 'fa-plus' ?>"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-12 text-center">
                    <a href="<?= BASE_URL ?>/pages/cart.php" class="bg-white border-2 border-slate-200 text-slate-600 hover:text-primary hover:border-primary px-8 py-3 rounded-full font-bold shadow-sm hover:shadow-md smooth-transition active:scale-95 cursor-pointer no-underline inline-flex items-center gap-2">
                        <i class="fa-solid fa-cart-shopping"></i> Lihat Keranjang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="toast-stack" style="position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px"></div>
<style>@keyframes slideIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:translateX(0)}}</style>
<script>
const BASE_URL = '<?= BASE_URL ?>';

function showToast(msg, type = "info", dur = 3200) {
    const colors = { info: "#1D4ED8", success: "#10B981", error: "#EF4444", warning: "#F59E0B" };
    const icons = { info: "fa-circle-info", success: "fa-circle-check", error: "fa-circle-xmark", warning: "fa-triangle-exclamation" };
    const iconBgs = { info: "#EFF6FF", success: "#ECFDF5", error: "#FEF2F2", warning: "#FFFBEB" };
    const stack = document.getElementById("toast-stack");
    const toast = document.createElement("div");
    toast.style.cssText = `display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:16px;background:#fff;box-shadow:0 8px 30px -4px rgba(0,0,0,0.15);min-width:280px;font-size:14px;font-weight:600;color:#0F172A;border-left:4px solid ${colors[type]};animation:slideIn .3s ease`;
    toast.innerHTML = `<div style="width:34px;height:34px;border-radius:10px;background:${iconBgs[type]};color:${colors[type]};display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fa-solid ${icons[type]}"></i></div><div style="flex:1">${msg}</div><button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:18px;padding:0;line-height:1">x</button>`;
    stack.appendChild(toast);
    setTimeout(() => {
        toast.style.transition = "all .3s";
        toast.style.opacity = "0";
        toast.style.transform = "translateX(20px)";
        setTimeout(() => toast.remove(), 300);
    }, dur);
}

function syncCart(count) {
    const badge = document.getElementById("cart-count");
    if (!badge) {
        return;
    }
    if (count > 0) {
        badge.textContent = count;
        badge.classList.remove("hidden");
    } else {
        badge.textContent = "0";
        badge.classList.add("hidden");
    }
}

async function addToCartDirect(productId, name) {
    try {
        const response = await fetch(`${BASE_URL}/api/marketplace/cart/add.php`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, qty: 1 })
        });
        const result = await response.json();
        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Produk gagal ditambahkan ke keranjang.');
        }

        syncCart(result.data.cart_count || 0);
        showToast(`<b>${name}</b> ditambahkan ke keranjang!`, "success");
    } catch (error) {
        showToast(error.message, "warning");
    }
}

function filterCategory(cat) {
    document.querySelectorAll(".cat-btn").forEach((button) => {
        button.className = button.className.replace("bg-primary text-white shadow-md shadow-blue-200", "bg-transparent text-slate-600");
    });

    const active = document.querySelector(`.cat-btn[data-cat="${cat}"]`);
    if (active) {
        active.className = active.className.replace("bg-transparent text-slate-600", "bg-primary text-white shadow-md shadow-blue-200");
    }

    document.querySelectorAll(".product-card").forEach((card) => {
        card.style.display = cat === "Semua Produk" || card.dataset.category === cat ? "" : "none";
    });
}

function sortProducts(mode) {
    const grid = document.getElementById('product-grid');
    const cards = [...grid.querySelectorAll('.product-card')];
    cards.sort((a, b) => {
        if (mode === 'price') {
            return Number(a.dataset.price) - Number(b.dataset.price);
        }
        return a.dataset.name.localeCompare(b.dataset.name, 'id');
    });
    cards.forEach((card) => grid.appendChild(card));
}

document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById('market-search');
    searchInput.addEventListener("input", function () {
        const query = this.value.toLowerCase().trim();
        document.querySelectorAll(".product-card").forEach((card) => {
            const matchesText = card.dataset.name.includes(query);
            const categoryButton = document.querySelector('.cat-btn.bg-primary');
            const activeCategory = categoryButton ? categoryButton.dataset.cat : 'Semua Produk';
            const matchesCategory = activeCategory === 'Semua Produk' || card.dataset.category === activeCategory;
            card.style.display = matchesText && matchesCategory ? "" : "none";
        });
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
